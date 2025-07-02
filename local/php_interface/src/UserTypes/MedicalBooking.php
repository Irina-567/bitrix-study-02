<?php

namespace UserTypes;

class MedicalBooking
{
    public static function GetUserTypeDescription()
    {
        return [
            'PROPERTY_TYPE' => 'S',
            'USER_TYPE' => 'medical_booking',
            'DESCRIPTION' => 'Procedures for booking a doctor appointment',
            'GetPropertyFieldHtml' => [__CLASS__, 'GetPropertyFieldHtml'],
            'GetPublicViewHTML' => [__CLASS__, 'GetPublicViewHTML'],
            'GetPublicEditHTML' => [__CLASS__, 'GetPublicEditHTML'],
            'GetAdminEditHTML' => [__CLASS__, 'GetAdminEditHTML'],
            'ConvertToDB' => [__CLASS__, 'ConvertToDB'],
            'ConvertFromDB' => [__CLASS__, 'ConvertFromDB'],
        ];
    }

    private static function RenderBooking($doctorId)
    {
        if (!$doctorId) {
            return '<em style="color:gray;">doctor is unknown</em>';
        }

        //fetch doctor's procedures
        $linkedProcedures = [];

        $propRes = \CIBlockElement::GetProperty(19, $doctorId, ['sort' => 'asc'], ['CODE' => 'MEDICAL_PROCEDURE_IDS']);

        while ($prop = $propRes->Fetch()) {
            if (!empty($prop['VALUE'])) {
                $linkedProcedures[] = (int)$prop['VALUE'];
            }
        }

        if (empty($linkedProcedures)) {
            return '<em style="color:gray;">no linked procedures</em>';
        }

        //get procedures by IDs
        $procedureBlocks = [];

        $procRes = \CIBlockElement::GetList([], [
            'IBLOCK_ID' => 18,
            'ID' => $linkedProcedures
        ], false, false, ['ID', 'NAME']);

        while ($proc = $procRes->GetNext()) {
            $procId = $proc['ID'];
            $procName = htmlspecialchars($proc['NAME']);

            $procedureBlocks[] = "<div class=\"booking-proc-inline\" data-proc-id=\"{$procId}\" data-doctor-id=\"{$doctorId}\">{$procName}</div>";
        }

        //popup content
        $html = '<div class="booking-procs-inline">' . implode('', $procedureBlocks) . '</div>';

        $html .= '
        <style>
            .booking-proc-inline {
                display: inline-block;
                margin: 2px;
                padding: 4px 6px;
                background: #eef;
                border-radius: 4px;
                font-size: 13px;
                cursor: pointer;
            }
            .booking-proc-inline:hover {
                background: #cce;
            }
        </style>

        <script>
            document.querySelectorAll(".booking-proc-inline").forEach(function(el) {
                el.addEventListener("click", function(e) {
                    e.preventDefault();

                    var procId = el.dataset.procId;
                    var doctorId = el.dataset.doctorId;

                    var popupContent = `
                        <form id="booking-form-${procId}">
                            <input type="hidden" name="procedure_id" value="${procId}">
                            <input type="hidden" name="doctor_id" value="${doctorId}">
                            <label>Patient\'s name:</label><br>
                            <input type="text" name="patient_name" required style="width: 100%; margin-bottom: 10px;"><br>

                            <label>Date and time:</label><br>
                            <div style="display: flex; justify-content: space-between; gap: 2px;">
                                <input type="text" name="booking_time" id="booking_time_${procId}" readonly
                                    style="width: calc(100% - 30px); display: inline-block;">
                                <button type="button" id="calendar_btn_${procId}" style="width: 28px; padding: 2px;">📅</button>
                            </div>
                            <br>
                            <button type="submit" style="width: 100%;">Create booking</button>
                        </form>
                    `;

                    var popup = BX.PopupWindowManager.create("booking_popup_" + procId, null, {
                        content: popupContent,
                        autoHide: true,
                        closeIcon: true,
                        titleBar: "Create booking",
                        buttons: [],
                        events: {
                            onAfterPopupShow: function () {
                                BX.ready(function () {
                                const input = document.getElementById("booking_time_" + procId);
                                const btn = document.getElementById("calendar_btn_" + procId);
                            
                                BX.bind(btn, "click", function () {
                                    BX.calendar({
                                        node: btn,
                                        field: input,
                                        bTime: true,
                                        bHideTime: false,
                                        b24Hour: true,
                                        callback: function (value) {
                                            input.value = value;
                                        }
                                    });
                                });
                            });
                                document.getElementById("booking-form-" + procId).addEventListener("submit", function (e) {
                                    e.preventDefault();

                                    var formData = new FormData(this);

                                    fetch("/local/ajax/create_booking.php", {
                                        method: "POST",
                                        body: formData
                                    })
                                    .then(response => response.json())
                                    .then(result => {
                                        if (result.success) {
                                            alert("Booking is creates successfully!");
                                            popup.close();
                                        } else {
                                            alert("Error: " + result.error);
                                        }
                                    })
                                    .catch(() => alert("Error sending request"));
                                });
                            }
                        }
                    });

                    popup.show();
                });
            });
        </script>
    ';

        return $html;
    }

    public static function GetPublicViewHTML($arProperty, $value, $strHTMLControlName)
    {
        $doctorId = isset($arProperty['ELEMENT_ID']) ? (int)$arProperty['ELEMENT_ID'] : 0;
        return self::RenderBooking($doctorId);
    }

    public static function GetPublicEditHTML($arProperty, $value, $strHTMLControlName)
    {
        $doctorId = isset($arProperty['ELEMENT_ID']) ? (int)$arProperty['ELEMENT_ID'] : 0;
        return self::RenderBooking($doctorId);
    }

    public static function GetPropertyFieldHtml($arProperty, $value, $strHTMLControlName)
    {
        $doctorId = $_REQUEST['ID'] ?? 0;
        return self::RenderBooking($doctorId);

    }

    public static function GetAdminEditHTML($arProperty, $value, $strHTMLControlName)
    {
        return '<div></div>';
    }

    public static function ConvertToDB($arProperty, $value)
    {
        return ['VALUE' => (int)$_REQUEST['ID']];
    }

    public static function ConvertFromDB($arProperty, $value)
    {
        return ['VALUE' => $value['VALUE']];
    }
}