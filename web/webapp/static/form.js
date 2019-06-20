    var forms = {
        "VERSION": "version",
        "KERS_MIN_SPEED": "kers_min_speed",
        "KERS_DIVIDOR": "kers_dividor",
        "MAX_SPEED": "max_speed",
        "VERSION_SPOOFING": "version_spoofing",
        "MOTOR_POWER_CONSTANT": "motor_power_constant",
		"CRUISE_CONTROL_DELAY": "cruise_control_delay",
		"THROTTLE_ALG": "throttle_alg",
		"WHEEL_SPEED_CONST": "wheel_speed_const",
		"MOTOR_START_SPEED": "motor_start_speed",
		"STAY_ON_LOCKED": "stay_on_locked"
    };

    var formValues = Object.values(forms);
    var queryStrings = window.location.search.substring(1);
    var queries = queryStrings.split('&');

    for (var i = 0; i < queries.length; i++) {
        var query = queries[i];
        var tmp = query.split('=');
        var found = false;

        for (var j = 0; j < formValues.length; j++) {
            if (formValues[j] === tmp[0]) {
                found = true;
                break;
            }
        }

        if(found) {
            if (tmp[1] === 'on') {
                tmp[1] = true;
            }

            ChangeForm(tmp[0], tmp[1], true);
        }
    }

    function GetForm(name) {
        return document.getElementsByName(name)[0];
    }

    function GetFormValue(name) {
        var o = GetForm(name);

        if (o.type === "checkbox") {
            return o.checked;
        }

        return o.value;
    }

    function GetPatchCheckBox(name) {
        var form = GetForm(name);
        return form.nextElementSibling && form.nextElementSibling.children[0];
    }

    function CheckForm(name, cb) {
        GetForm(name).disabled = !cb.checked;
    }

    function ChangeForm(name, value, patch) {
        var o = GetForm(name);

        if (o.type === "checkbox") {
            o.checked = value;
        } else {
            o.value = value;
        }

        if (typeof patch === 'boolean') {
            var cb = GetPatchCheckBox(name);

            if (cb) {
                cb.checked = patch;
                cb.onchange();
            }
        }
    }

    function Preset_Default() {
        ChangeForm(forms.VERSION, "DRV133");
        ChangeForm(forms.KERS_MIN_SPEED, "6", false);
        ChangeForm(forms.MAX_SPEED, "32", false);
        ChangeForm(forms.KERS_DIVIDOR, "2", false);
        ChangeForm(forms.MOTOR_POWER_CONSTANT, "51575", false);
		ChangeForm(forms.CRUISE_CONTROL_DELAY, "5", false);
        ChangeForm(forms.VERSION_SPOOFING, false);
		ChangeForm(forms.THROTTLE_ALG, false);
		ChangeForm(forms.MOTOR_START_SPEED, "5", false);
		ChangeForm(forms.WHEEL_SPEED_CONST, "390", false);
		ChangeForm(forms.STAY_ON_LOCKED, false);
    }
    function Preset_SH() {
        ChangeForm(forms.VERSION, "DRV133");
        ChangeForm(forms.KERS_MIN_SPEED, "6", false);
        ChangeForm(forms.MAX_SPEED, "33", true);
        ChangeForm(forms.KERS_DIVIDOR, "2", false);
        ChangeForm(forms.MOTOR_POWER_CONSTANT, "50000", true);
		ChangeForm(forms.CRUISE_CONTROL_DELAY, "5", false);
        ChangeForm(forms.VERSION_SPOOFING, true);
		ChangeForm(forms.THROTTLE_ALG, false);
		ChangeForm(forms.MOTOR_START_SPEED, "5", false);
		ChangeForm(forms.WHEEL_SPEED_CONST, "390", false);
		ChangeForm(forms.STAY_ON_LOCKED, true);
    }

    function Preset_Sport() {
        ChangeForm(forms.VERSION, "DRV133");
        ChangeForm(forms.KERS_MIN_SPEED, "6", false);
        ChangeForm(forms.MAX_SPEED, "45", true);
        ChangeForm(forms.KERS_DIVIDOR, "2", false);
        ChangeForm(forms.MOTOR_POWER_CONSTANT, "45000", true);
		ChangeForm(forms.CRUISE_CONTROL_DELAY, "5", false);
        ChangeForm(forms.VERSION_SPOOFING, false);
		ChangeForm(forms.THROTTLE_ALG, true);
		ChangeForm(forms.WHEEL_SPEED_CONST, "390", false);
		ChangeForm(forms.STAY_ON_LOCKED, false);
    }

    function Share() {
        var url = location.protocol + '//' + location.host;
        var firstParam = true;

        function getSeparator() {
            if (firstParam) {
                firstParam = false;
                return '?';
            }

            return '&';
        }

        for (var i = 0; i < formValues.length; i++) {
            var form = formValues[i];

            var formValue = GetFormValue(form);
            var patchCheckbox = GetPatchCheckBox(form);

            if (patchCheckbox) {
                if (patchCheckbox.checked) {
                    url += getSeparator() + form + '=' + formValue;
                }
            } else if (typeof formValue === 'boolean') {
                if (formValue) {
                    url += getSeparator() + form + '=on';
                }
            } else {
                url += getSeparator() + form + '=' + formValue;
            }
        }

        var textArea = document.createElement("textarea");

        textArea.value = url;
        document.body.appendChild(textArea);

        textArea.focus();
        textArea.select();

        document.execCommand('copy');
        document.getElementById('shareConfirmation').innerText = 'Copied!';

        document.body.removeChild(textArea);
    }