<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <meta name="description" content="Ninebot ES/SNSC Custom Firmware Toolkit">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="/static/favicon.png">
    <meta name="author" content="BotoX">

    <title>Ninebot ES/SNSC Custom Firmware Toolkit</title>

    <link
      rel="stylesheet"
      href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css"
      integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T"
      crossorigin="anonymous"
    >

    <link
      rel="stylesheet"
      href="https://getbootstrap.com/docs/4.3/assets/css/docs.min.css"
      crossorigin="anonymous"
    >
  </head>
  <body>

    <div class="container-fluid">
      <div class="row">
        <div class="col">
          <nav aria-label="back">
            <a href="https://scooterhacking.org/">
              <i class="fas fa-arrow-left"></i>
            </a>
          </nav>
        </div>
        <div class="col">
          <a href="https://sctrhck.ml/discord" class="float-right">
            <img
              src="https://discordapp.com/assets/e4923594e694a21542a489471ecffa50.svg"
              alt="Join Discord"
              height="33"
              width="105"
            >
          </a>
        </div>
      </div>
    </div>

    <div class="container-fluid">
      <h1 class="text-center">Ninebot ES/SNSC Custom Firmware Toolkit</h1>

      <div class="row-fluid justify-content-center">
        <div class="col">
          <div class="bd-callout bd-callout-warning">
            <h3>Disclaimer</h5>

            <p>
              Configure your own custom firmware by adjusting the options below. <br>
              There are safety checks in place to ensure your scooter will not be bricked. <br>
              Be aware that a higher motor power will shorten the lifetime of your battery and could damage your motor. <br>
              By default nothing will be patched, enable patches with the "Patch?" checkbox next to them. <br>
              Please remember that ScooterHacking.org or its members can not be held responsible for any damage you may cause to yourself or your scooter. <br>
              The software is provided "as is", without warranty of any kind, express or implied, including but not limited to the warranties of merchantability, fitness for a particular purpose and noninfringement. <br>
              In no event shall the authors or copyright holders be liable for any claim, damages or other liability, wether in an action of contract, tort or otherwise, arising from, out of or in connection with the software or the use or other other dealings in the software. 
            </p>
          </div>
        </div>
      </div>

      <div class="row-fluid">
        <form>
          <div class="form-group row">
            <label for="driver-version" class="col-sm-2 col-form-label">Presets</label>
            <div class="col-sm-10">
              <div class="btn-group" role="group" aria-label="Presets">
                <button type="button" class="btn btn-primary preset preset--default">Default</button>
                <button type="button" class="btn btn-primary preset preset--es4">ES4</button>
                <button type="button" class="btn btn-primary preset preset--es4-l">ES4-L</button>
                <button type="button" class="btn btn-primary preset preset--es2mod">ES2Mod</button>
              </div>
            </div>
          </div>
        </form>
      </div>

      <div class="row-fluid">
        <hr>
      </div>

      <div class="row-fluid">
        <form action="/cfw" onsubmit="return confirm('Do you really want to generate your CFW? This is an experimental tool!');">
          <!-- Driver Version -->
          <div class="form-group row">
            <label for="driver-version" class="col-sm-2 col-form-label">Base version for your firmware</label>
            <div class="col-sm-10">
              <select
                name="version"
                class="custom-select mr-sm-2"
                id="driver-version"
                aria-describedby="driver-version-help"
              >
                <option value="DRV120">1.2.0</option>
                <option value="DRV133">1.3.3</option>
                <option value="DRV139">1.3.9</option>
                <option value="DRV150">1.5.0</option>
                <option value="DRV151" selected>1.5.1</option>
              </select>
              <small id="driver-version-help" class="form-text text-muted">
                1.3+ is recommended over 1.2.0, because you most likely won't have to downgrade the BMS.
              </small>
            </div>
          </div>

          <!-- Output file -->
          <div class="form-group row">
            <label for="output-file" class="col-sm-2 col-form-label">Output file</label>
            <div class="col-sm-10">
              <select
                name="output"
                class="custom-select mr-sm-2"
                id="output-file"
                aria-describedby="output-file-help"
              >
                <option value="zip" selected>ZIP</option>
                <option value="enc">Encoded bin</option>
                <option value="bin">Bin</option>
              </select>
              <small id="output-file-help" class="form-text text-muted">
                Select <b>ZIP</b> if you want to flash using <a href="https://sctrhck.ml/ninebotfun">NinebotFun</a> / <a href="https://sctrhck.ml/esdowng">ES DownG</a>.<br />
                Select <b>Encoded bin</b> if you want to flash using <a href="https://sctrhck.ml/ninebotflasher">Ninebot-Flasher</a> / py9b / NineRift.<br />
                Select <b>Bin</b> if you know what you're doing. Don't flash that file.
              </small>
            </div>
          </div>

          <!-- KERS Minimum Speed -->
          <div class="form-group row">
            <label for="kers-minimum-speed" class="col-sm-2 col-form-label">Kers minimum speed</label>
            <div class="col-sm-10">
              <div class="input-group">
                <input name="kers_min_speed" type="number" class="form-control" value="6" disabled>
                <div class="input-group-append">
                  <label class="input-group-text">
                    <input class="patch-input" type="checkbox"> &nbsp;Patch?
                  </label>
                </div>
              </div>
              <small id="kers-minimum-speed-help" class="form-text text-muted">
                Speed in km/h at which the scooter will start braking on it's own when the motor is not active. <br>
                You will still have recuperative braking when using the brake lever. <br>
                If you want "KERS OFF" then just put this to 40km/h. <br>
                <span class="text-danger font-weight-bold">
                  Disabling KERS might result in failure of the control board and in injuries caused by strong braking. (...)
                </span>
              </small>
            </div>
          </div>

          <!-- KERS Dividor -->
          <div class="form-group row">
            <label for="kers-dividor" class="col-sm-2 col-form-label">Kers dividor</label>
            <div class="col-sm-10">
              <div class="input-group">
                <input name="kers_dividor" type="number" class="form-control" value="2" disabled>
                <div class="input-group-append">
                  <label class="input-group-text">
                    <input class="patch-input" type="checkbox"> &nbsp;Patch?
                  </label>
                </div>
              </div>
              <small id="kers-dividor-help" class="form-text text-muted">
                At which factor the KERS strength is divided. <br>
                <span class="font-italic">
                  Only 6 and 2 are available to prevent bricks.
                </span>
              </small>
            </div>
          </div>

          <!-- Maximum Speed -->
          <div class="form-group row">
            <label for="maximum-speed" class="col-sm-2 col-form-label">Maximum Speed</label>
            <div class="col-sm-10">
              <div class="input-group">
                <input name="max_speed" type="number" class="form-control" value="32" min="0" max="35" disabled>
                <div class="input-group-append">
                  <label class="input-group-text">
                    <input class="patch-input" type="checkbox"> &nbsp;Patch?
                  </label>
                </div>
              </div>
              <small id="maximum-speed-help" class="form-text text-muted">
                The scooter will stay below this speed. Use it if you're running at limited speed even in Sport mode.
              </small>
            </div>
          </div>

          <!-- Cruise Control Delay -->
          <div class="form-group row">
            <label for="cruise-control-delay" class="col-sm-2 col-form-label">Cruise Control Delay</label>
            <div class="col-sm-10">
              <div class="input-group">
                <input name="cruise_control_delay" type="number" step="1" min="1" max="100" value="5" class="form-control" disabled>
                <div class="input-group-append">
                  <label class="input-group-text">
                    <input class="patch-input" type="checkbox"> &nbsp;Patch?
                  </label>
                </div>
              </div>
              <small id="cruise-control-delay-help" class="form-text text-muted">
                How many seconds it takes for cruise control to kick in.
              </small>
            </div>
          </div>

          <!-- Motor Start Speed -->
          <div class="form-group row">
            <label for="motor-start-speed" class="col-sm-2 col-form-label">Motor Start Speed</label>
            <div class="col-sm-10">
              <div class="input-group">
                <input name="motor_start_speed" step="1" min="0" max="10" type="number" class="form-control" value="5" disabled>
                <div class="input-group-append">
                  <label class="input-group-text">
                    <input class="patch-input" type="checkbox"> &nbsp;Patch?
                  </label>
                </div>
              </div>
              <small id="motor-start-speed-help" class="form-text text-muted">
                Minimum speed in km/h before the motor will start.
              </small>
            </div>
          </div>

          <!-- Motor Power Constant -->
          <div class="form-group row">
            <label for="motor-power-constant" class="col-sm-2 col-form-label">Motor Power Constant</label>
            <div class="col-sm-10">
              <div class="input-group">
                <input class="form-control" name="motor_power_constant" type="number" step="1" min="40000" max="65535" value="51575" disabled>
                <div class="input-group-append">
                  <label class="input-group-text">
                    <input class="patch-input" type="checkbox"> &nbsp;Patch?
                  </label>
                </div>
              </div>
              <small id="motor-power-constant-help" class="form-text text-muted">
                Lower = More power. Too much power is not recommended for battery and motor life. <br>
                <span class="text-danger font-weight-bold">
                  Modifying this value without knowing what you're doing will surely break your scooter.
                </span>
              </small>
            </div>
          </div>

          <!-- Wheel Speed Multiplier -->
          <div class="form-group row">
            <label for="wheel-speed-multiplier" class="col-sm-2 col-form-label">Wheel Speed Multiplier</label>
            <div class="col-sm-10">
              <div class="input-group">
                <input name="wheel_speed_const" type="number" class="form-control" step="1" min="200" max="500" value="390" disabled>
                <div class="input-group-append">
                  <label class="input-group-text">
                    <input class="patch-input" type="checkbox"> &nbsp;Patch?
                  </label>
                </div>
              </div>
              <small id="wheel-speed-multiplier-help" class="form-text text-muted">
                For 10" wheels use 315, don't change otherwise. <span class="badge badge-danger">Experimental!</span>
              </small>
            </div>
          </div>

          <!-- Version Spoofing -->
          <div class="form-group row">
            <div class="col">
              <div class="custom-control custom-switch">
                <input name="version_spoofing" type="checkbox" class="custom-control-input" id="version-spoofing">
                <label class="custom-control-label" for="version-spoofing">Version spoofing</label>
              </div>
              <small id="version-spoofing-help" class="form-text text-muted">
                Makes the version number higher to prevent updates from the Ninebot app. <span class="badge badge-primary">Updated!</span>
              </small>
            </div>
          </div>

          <!-- Enable SWD -->
          <div class="form-group row">
            <div class="col">
              <div class="custom-control custom-switch">
                <input name="swd_enable" type="checkbox" class="custom-control-input" id="swd-enable">
                <label class="custom-control-label" for="swd-enable">Enable SWD</label>
              </div>
              <small id="swd-enable-help" class="form-text text-muted">
                Enables the SWD interface, allowing real-time debugging using ST-Link. <span class="badge badge-warning">Testing!</span>
              </small>
            </div>
          </div>

          <!-- Bypass Ninebot BMS requirement -->
          <div class="form-group row">
            <div class="col">
              <div class="custom-control custom-switch">
                <input name="bypass_BMS" type="checkbox" class="custom-control-input" id="bypass-bms">
                <label class="custom-control-label" for="bypass-bms">Bypass Ninebot BMS requirement</label>
              </div>
              <small id="bypass-bms-help" class="form-text text-muted">
                Allows the scooter to run on any battery without the need of a Ninebot BMS.
                <span class="badge badge-danger">Experimental!</span><br>
                Warning: the scooter won't provide any info on the current battery state, voltage, or remaining capacity.
              </small>
            </div>
          </div>

          <!-- Current-based Throttle Algorithm -->
          <div class="form-group row">
            <div class="col">
              <div class="custom-control custom-switch">
                <input name="throttle_alg" type="checkbox" class="custom-control-input" id="current-based-throttle-algorithm">
                <label class="custom-control-label" for="current-based-throttle-algorithm">Current-based Throttle Algorithm</label>
              </div>
              <small id="current-based-throttle-algorithm-help" class="form-text text-muted">
                Instead of speed-based, the throttle will work on a power-based algorithm (like in a thermal engine vehicle).<br />
                Max speed is ignored if you enable this.
              </small>
            </div>
          </div>

          <!-- Stay on when scooter is locked -->
          <div class="form-group row">
            <div class="col">
              <div class="custom-control custom-switch">
                <input name="stay_on_locked" type="checkbox" class="custom-control-input" id="stay-on-when-scooter-on">
                <label class="custom-control-label" for="stay-on-when-scooter-on">Stay on when scooter is locked</label>
              </div>
              <small id="stay-on-when-scooter-on-help" class="form-text text-muted">
                Disables auto shutdown when the scooter is locked so it stays on forever. <span class="badge badge-primary">Updated!</span>
              </small>
            </div>
          </div>

          <!-- Remove Charging Mode -->
          <div class="form-group row">
            <div class="col">
              <div class="custom-control custom-switch">
                <input name="remove_charging_mode" type="checkbox" class="custom-control-input" id="remove-charging-mode">
                <label class="custom-control-label" for="remove-charging-mode">Remove Charging Mode</label>
              </div>
              <small id="remove-charging-mode-help" class="form-text text-muted">
                ESC will ignore input from the charging line, scooter will be rideable during charge. Useful for non-official external batteries.
              </small>
            </div>
          </div>

          <!-- Change ESC<->BMS baud rate to 76800 -->
          <div class="form-group row">
            <div class="col">
              <div class="custom-control custom-switch">
                <input name="bms_uart_76800" type="checkbox" class="custom-control-input" id="change-esc-bms-rate">
                <label class="custom-control-label" for="change-esc-bms-rate">Change ESC<->BMS baud rate to 76800</label>
              </div>
              <small id="change-esc-bms-rate-help" class="form-text text-muted">
                Only if you use the <a href="https://ninebot.scooterhacking.org/#">nonexistent compatible open source BMS!</a>
              </small>
            </div>
          </div>

          <!-- Understanding risks -->
          <div class="form-group row">
            <div class="col">
              <input type="checkbox"> I understand the risks and I am responsible for entered values
              <small class="form-text text-muted">Make sure to double check all of your entered values before submitting!</small>
            </div>
          </div>

          <div class="jumbotron jumbotron-fluid">
            <div class="col">
              <button class="btn btn-secondary">Share config</button>
              <button class="btn btn-primary float-right" type="submit">Submit form</button>
            </div>
          </div>
        </form>

        <hr>

        <div class="alert alert-info">
          <span class="text-danger font-weight-bold">⚠ NEW</span> The tool now makes .zip files with both encrypted and unencrypted firmware and an info.txt inside.
        </div>
        <div class="alert alert-info">
          <span class="text-danger font-weight-bold">⚠ NEW</span> Use the following Windows app made by majsi to flash your modified firmware: <a href="https://www.microsoft.com/en-us/p/ninebot-flasher/9p5hws0hq55s?activetab=pivot:overviewtab" class="alert-link">Ninebot-Flasher</a>
        </div>
        <div class="alert alert-info">
          <span class="text-danger font-weight-bold">⚠ NEW</span> Use the following Android app made by CamiAlfa to flash your modified firmware: <a href="https://play.google.com/store/apps/details?id=com.esdowngrade" class="alert-link">ES DownG</a>
        </div>

        <hr>

        <div class="alert alert-primary">
          <i class="fas fa-code-branch"></i> Source code on <a href="https://github.com/scooterhacking/ninebot-es-snsc-firmware-patcher" class="alert-link">GitHub</a>
        </div>

        <div class="alert alert-primary">
          <h4 class="alert-heading">Support us</h4>
          <hr>
          <i class="fas fa-hand-holding-usd"></i> Donate to <a href="https://www.paypal.me/majsinko" class="alert-link">majsi (CFW scripting)</a><br>
          <i class="fas fa-hand-holding-usd"></i> Donate to <a href="https://www.paypal.me/camialfa" class="alert-link">CamiAlfa (ES DownG)</a><br>
          <i class="fas fa-hand-holding-usd"></i> Donate to <a href="https://paypal.me/scooterhacking" class="alert-link">ScooterHacking.org (host and maintainer)</a><br>
          <i class="fas fa-hand-holding-usd"></i> Donate to <a href="https://paypal.me/BotoXbz" class="alert-link">Botox (original creator of the M365 CFW toolkit)</a><br>
        </div>
      </div>
    </div>

    <!-- Scripts -->
    <script
      src="https://code.jquery.com/jquery-3.3.1.slim.min.js"
      integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo"
      crossorigin="anonymous"
    ></script>

    <script
      src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"
      integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1"
      crossorigin="anonymous"
    ></script>

    <script
      src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"
      integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM"
      crossorigin="anonymous"
    ></script>

    <script src="https://kit.fontawesome.com/caaa8631ad.js"></script>

    <script src="/static/form.js" type="text/javascript"></script>

    <script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
  </body>
</html>