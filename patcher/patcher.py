#!/usr/bin/python3
from binascii import hexlify
import struct
import keystone
from xiaotea import XiaoTea

# https://web.eecs.umich.edu/~prabal/teaching/eecs373-f10/readings/ARMv7-M_ARM.pdf
MOVW_T3_IMM = [*[None]*5, 11, *[None]*6, 15, 14, 13, 12, None, 10, 9, 8, *[None]*4, 7, 6, 5, 4, 3, 2, 1, 0]
MOVS_T1_IMM = [*[None]*8, 7, 6, 5, 4, 3, 2, 1, 0]

def PatchImm(data, ofs, size, imm, signature):
    assert size % 2 == 0, 'size must be power of 2!'
    assert len(signature) == size * 8, 'signature must be exactly size * 8 long!'
    imm = int.from_bytes(imm, 'little')
    sfmt = '<' + 'H' * (size // 2)

    sigs = [signature[i:i + 16][::-1] for i in range(0, len(signature), 16)]
    orig = data[ofs:ofs+size]
    words = struct.unpack(sfmt, orig)

    patched = []
    for i, word in enumerate(words):
        for j in range(16):
            imm_bitofs = sigs[i][j]
            if imm_bitofs is None:
                continue

            imm_mask = 1 << imm_bitofs
            word_mask = 1 << j

            if imm & imm_mask:
                word |= word_mask
            else:
                word &= ~word_mask
        patched.append(word)

    packed = struct.pack(sfmt, *patched)
    data[ofs:ofs+size] = packed
    return (orig, packed)

class SignatureException(Exception):
    pass

def FindPattern(data, signature, mask=None, start=None, maxit=None):
    sig_len = len(signature)
    if start is None:
        start = 0
    stop = len(data)
    if maxit is not None:
        stop = start + maxit

    if mask:
        assert sig_len == len(mask), 'mask must be as long as the signature!'
        for i in range(sig_len):
            if signature[i] is not None:
                signature[i] &= mask[i]

    for i in range(start, stop):
        matches = 0

        while signature[matches] is None or signature[matches] == (data[i + matches] & (mask[matches] if mask else 0xFF)):
            matches += 1
            if matches == sig_len:
                return i

    raise SignatureException('Pattern not found!')


class FirmwarePatcher():
    def __init__(self, data):
        self.data = bytearray(data)
        self.ks = keystone.Ks(keystone.KS_ARCH_ARM, keystone.KS_MODE_THUMB)

    def encrypt(self):
        self.data = XiaoTea.XiaoTea().encrypt(self.data)
    #@author : ScooterHacking
    def version_spoofing(self, DRV_version):
        if DRV_version == "DRV133" or DRV_version == "DRV139":
            val = b'\x50'
            sig = [0x74, 0x01, 0x40, 0xF2, None, 0x10]
            ofs = FindPattern(self.data, sig) + 5
            pre, post = PatchImm(self.data, ofs, 2, val, MOVS_T1_IMM)
            return [(ofs, pre, post)]
    #@author : majsi
    def kers_min_speed(self, kmh):
        val = struct.pack('<H', int(kmh * 390))
        sig = [0x25, 0x68, 0x40, 0xF6, 0x24, *[None]*2, 0x42]
        ofs = FindPattern(self.data, sig) + 2
        pre, post = PatchImm(self.data, ofs, 4, val, MOVW_T3_IMM)
        return [(ofs, pre, post)]
    def bms_uart_76800(self):
        ofs = 0
        while True:
            sig = [0x00, 0x21, 0x4F, 0xF4, 0xE1, 0x30, 0x00, 0x90, 0xAD, 0xF8, 0x08, 0x10, 0x0C, 0x20, 0xAD, 0xF8, 0x0A, 0x00, 0x60, 0x15, 0xAD, 0xF8, 0x14, 0x00, 0xAD]
            ofs = FindPattern(self.data, sig) + 2

            # USART3 address
            sig = [0x00, 0x48, 0x00, 0x40]
            try:
                FindPattern(self.data, sig)
                break
            except SignatureException:
                continue
    #@author : majsi
    def normal_max_speed(self, kmh):
        val = struct.pack('<B', int(kmh))
        sig = [0x04, 0xE0, 0x21, 0x85, 0x1C, 0x21, 0xE1, 0x83]
        ofs = FindPattern(self.data, sig) + 4
        pre, post = PatchImm(self.data, ofs, 2, val, MOVS_T1_IMM)
        return [(ofs, pre, post)]
    #@author : majsi
    def kers_dividor_6(self, DRV_version):
        if DRV_version != "DRV147" and DRV_version != "DRV150" and DRV_version != "DRV151":
            sig = [0x00, 0xEB, 0x40, 0x00, 0x40, 0x00, None, None, 0x00, 0xEB, 0x40, 0x00]
        else:
            sig = [0x00, 0xEB, 0x80, 0x00, 0x40, 0x00, 0xA4, 0xF8, 0x7C, 0x00, 0x13, 0x48]
        ofs = FindPattern(self.data, sig)
        pre = self.data[ofs:ofs + 6]
        asm = f'''
                    ADD.W  R0, R0, R0
                    LSRS   R0, R0, #1
                '''
        post = bytes(self.ks.asm(asm)[0])
        self.data[ofs:ofs + 6] = post
        return [(ofs, pre, post)]
	#@author : majsi
    def motor_start_speed(self, kmh):
        ret = []
        val = int(kmh * 390)
        val = val - (val % 16)
        assert val.bit_length() <= 12, 'bit length overflow'
        sig = [0x4B, 0x01, None, None, 0x48, 0x00, None, None, None, 0xB6, 0xF5, 0xC3, 0x6F, 0x0D, 0xDD]
        ofs = FindPattern(self.data, sig) + 9
        pre = self.data[ofs:ofs + 4]
        post = bytes(self.ks.asm('CMP.W R6, #{:n}'.format(val))[0])
        self.data[ofs:ofs + 4] = post
        ret.append((ofs, pre, post))
        ofs += 4

        pre, post = self.data[ofs:ofs + 1], bytearray((0x0D, 0xDB))
        self.data[ofs:ofs + 2] = post
        ret.append((ofs, pre, post))
        return ret
	#@author : majsi
    def kers_dividor_2(self):
        sig = [0x00, 0xEB, 0x80, 0x00, None, 0x00, None, None]
        ofs = FindPattern(self.data, sig) + 6
        pre = self.data[ofs:ofs + 2]
        asm = f'''
                    LSRS   R0, R0, #0xC
                '''
        post = bytes(self.ks.asm(asm)[0])
        self.data[ofs:ofs + 2] = post
        return [(ofs, pre, post)]
	#@author : majsi
    def alt_throttle_alg(self):
        sig = [0xF0, 0xB5, 0x25, 0x4A, 0x00, 0x24, 0xA2, 0xF8, 0xEC, 0x40, 0x24, 0x49]
        ofs = FindPattern(self.data, sig) + 4
        pre, post = self.data[ofs:ofs + 1], bytearray((0x01, 0x24))
        self.data[ofs:ofs + 2] = post
        return [(ofs, pre, post)]
	#@author : majsi	
    def max_speed(self, kmh):
        ret = []
        val = struct.pack('<B', int(kmh))
        sig = [None, 0xF8, 0x34, 0xC0, None, None, 0x43, 0xF2]
        ofs = FindPattern(self.data, sig) + 4
        pre, post = PatchImm(self.data, ofs, 2, val, MOVS_T1_IMM)
        ret.append((ofs, pre, post))

        sig = [None, None, None, None, 0x17, None, None, 0x83, 0x46, 0xF6, 0x60]
        ofs = FindPattern(self.data, sig) + 4
        pre, post = PatchImm(self.data, ofs, 2, val, MOVS_T1_IMM)

        ret.append((ofs, pre, post))

        if self.data[0x7BAA] == 0x33 and self.data[0x7BAB] == 0x11:
            sig = [None, 0xF8, 0x2E, 0xE0, 0x22, 0x20, 0xE0, 0x83, 0x1B, 0xE0, 0x95]
            ofs = FindPattern(self.data, sig) + 4
            pre, post = PatchImm(self.data, ofs, 2, val, MOVS_T1_IMM)
        elif (self.data[0x8242] == 0xA8 and self.data[0x8243] == 0x71) or \
             (self.data[0x8246] == 0x51 and self.data[0x8247] == 0x11):
            sig = [0xDE, 0xD0, 0x11, 0xE0, 0x22, None, None, 0x83, 0xDE, 0xE7, 0x95]
            ofs = FindPattern(self.data, sig) + 4
            pre, post = PatchImm(self.data, ofs, 2, val, MOVS_T1_IMM)
        else:
            sig = [0x52, 0xC0, 0x4F, 0xF0, 0x22, 0x0E, 0x4C, 0xF2, 0x50, None, None]
            ofs = FindPattern(self.data, sig) + 4
            pre, post = PatchImm(self.data, ofs, 2, val, MOVS_T1_IMM)

        ret.append((ofs, pre, post))
        return ret
	#@author : majsi	
    def cruise_control_delay(self, delay):
        delay = int(delay * 200)
        assert delay.bit_length() <= 12, 'bit length overflow'
        sig = [0x48, 0xB0, 0xF8, 0xF8, None, 0x33, 0x4A, 0x4F, 0xF4, 0x7A, 0x71, 0x01, 0x28]
        mask = [0xFF, 0xFF, 0xFF, 0xFF, 0xFF, 0xF8, 0xFE, 0xFF, 0xFF, 0xFF, 0xFE, 0xFF, 0xFE]
        ofs = FindPattern(self.data, sig, mask) + 7
        pre = self.data[ofs:ofs+4]
        post = bytes(self.ks.asm('MOV.W R1, #{:n}'.format(delay))[0])
        self.data[ofs:ofs+4] = post
        return [(ofs, pre, post)]

    # lower value = more power
    # original = 51575 (~500 Watt)
    # DYoC = 40165 (~650 Watt)
    # CFW W = 27877 (~850 Watt)
    # CFW = 25787 (~1000 Watt)
	#@author : majsi
    def motor_power_constant(self, val):
        val = struct.pack('<H', int(val))
        ret = []
        sig = [0x31, 0x68, 0x2A, 0x68, 0x09, 0xB2, 0x09, 0x1B, 0x12, 0xB2, 0xD3, 0x1A, 0x4C, 0xF6, 0x77, 0x12]
        ofs = FindPattern(self.data, sig) + 12
        pre, post = PatchImm(self.data, ofs, 4, val, MOVW_T3_IMM)
        ret.append((ofs, pre, post))
        ofs += 4
 
        ofs += 4
        pre, post = PatchImm(self.data, ofs, 4, val, MOVW_T3_IMM)
        ret.append((ofs, pre, post))
 
        sig = [0xD3, 0x1A, 0x4C, 0xF6, 0x77, 0x12]
        ofs = FindPattern(self.data, sig, None, ofs, 100) + 2
        pre, post = PatchImm(self.data, ofs, 4, val, MOVW_T3_IMM)
        ret.append((ofs, pre, post))
        ofs += 4
 
        ofs += 4
        pre, post = PatchImm(self.data, ofs, 4, val, MOVW_T3_IMM)
        ret.append((ofs, pre, post))
 
        sig = [0xC9, 0x1B, 0x4C, 0xF6, 0x77, 0x13]
        ofs = FindPattern(self.data, sig, None, ofs, 100) + 2
        pre, post = PatchImm(self.data, ofs, 4, val, MOVW_T3_IMM)
        ret.append((ofs, pre, post))
        return ret
        #@author : Lothean
    def wheel_speed_const(self, val):
        val = struct.pack('<H', int(val))
        sig = [0xB4 ,0xF9, 0x1E, 0x00, 0x4F, 0xF4, 0xC3, 0x71]
        ofs = FindPattern(self.data, sig) + 4
        pre, post = PatchImm(self.data, ofs, 4, val, MOVW_T3_IMM)
        self.data[ofs:ofs+4] = post
        return [(ofs, pre, post)]
        #@author : Lothean
    def stay_on_locked(self):
        sig = [None, 0x49, 0x40, 0x1C, *[None]*2, 0x88, 0x42, None, 0xDB, *[None]*4]
        ofs = FindPattern(self.data, sig) + 14
        pre = self.data[ofs:ofs+4]
        post = bytes(self.ks.asm('NOP;NOP')[0])
        self.data[ofs:ofs+4] = post
        return [(ofs, pre, post)]


def eprint(*args, **kwargs):
    print(*args, file=sys.stderr, **kwargs)

if __name__ == "__main__":
    import sys
    if len(sys.argv) != 3:
        eprint("Usage: {0} <orig-firmware.bin> <target.bin>".format(sys.argv[0]))
        exit(1)

    with open(sys.argv[1], 'rb') as fp:
        data = fp.read()

    cfw = FirmwarePatcher(data)

    cfw.version_spoofing()
    cfw.kers_min_speed(6)
    cfw.max_speed(32)
    #cfw.kers_dividor_6()
    cfw.motor_start_speed(6)
    #cfw.kers_dividor_2()
    #cfw.alt_throttle_alg()
    #cfw.cruise_control_delay(5)
    #cfw.motor_power_constant(48000)
    #cfw.wheel_speed_const(420)
    cfw.stay_on_locked()

    cfw.encrypt()

    with open(sys.argv[2], 'wb') as fp:
        fp.write(cfw.data)
