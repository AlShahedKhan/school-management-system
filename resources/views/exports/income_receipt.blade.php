<!DOCTYPE html>
<html lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Money Receipt #{{ $income->id }}</title>
    <style>
        /* Using 'DejaVu Sans' because it is built into DomPDF
           and supports Unicode characters/symbols better than Helvetica */
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 13px;
            line-height: 1.6;
            color: #333;
        }

        .receipt-box {
            border: 2px solid #333;
            padding: 20px;
            width: 100%;
            margin: auto;
        }

        .header {
            text-align: center;
            border-bottom: 1px dashed #ccc;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }

        .header h2 {
            margin: 0;
            color: #1a56db;
            text-transform: uppercase;
        }

        .header p {
            margin: 5px 0;
            font-size: 11px;
            color: #555;
        }

        .receipt-title {
            text-align: center;
            background: #333;
            color: #fff;
            padding: 5px;
            width: 150px;
            margin: 10px auto;
            font-weight: bold;
            border-radius: 4px;
        }

        .info-table {
            width: 100%;
            margin-top: 10px;
        }

        .info-table td {
            padding: 5px 0;
            border-bottom: 1px dotted #eee;
        }

        .label {
            font-weight: bold;
            width: 120px;
        }

        .amount-section {
            margin-top: 20px;
            padding: 10px;
            border: 1px solid #333;
            display: inline-block;
            font-size: 16px;
            font-weight: bold;
        }

        .footer-table {
            width: 100%;
            margin-top: 60px;
        }

        .footer-table td {
            text-align: center;
            width: 50%;
        }

        .signature-space {
            border-top: 1px solid #333;
            width: 150px;
            margin: auto;
            padding-top: 5px;
        }
    </style>
</head>

<body>

    @php
        function convertNumberToWords($number)
        {
            $dictionary = [
                0 => 'zero',
                1 => 'one',
                2 => 'two',
                3 => 'three',
                4 => 'four',
                5 => 'five',
                6 => 'six',
                7 => 'seven',
                8 => 'eight',
                9 => 'nine',
                10 => 'ten',
                11 => 'eleven',
                12 => 'twelve',
                13 => 'thirteen',
                14 => 'fourteen',
                15 => 'fifteen',
                16 => 'sixteen',
                17 => 'seventeen',
                18 => 'eighteen',
                19 => 'nineteen',
                20 => 'twenty',
                30 => 'thirty',
                40 => 'forty',
                50 => 'fifty',
                60 => 'sixty',
                70 => 'seventy',
                80 => 'eighty',
                90 => 'ninety',
                100 => 'hundred',
                1000 => 'thousand',
                1000000 => 'million',
            ];
            if (!is_numeric($number)) {
                return false;
            }
            if ($number < 21) {
                return $dictionary[$number];
            }
            if ($number < 100) {
                $tens = ((int) ($number / 10)) * 10;
                $units = $number % 10;
                return $dictionary[$tens] . ($units ? '-' . $dictionary[$units] : '');
            }
            if ($number < 1000) {
                $hundreds = (int) ($number / 100);
                $remainder = $number % 100;
                $res = $dictionary[$hundreds] . ' ' . $dictionary[100];
                if ($remainder) {
                    $res .= ' and ' . convertNumberToWords($remainder);
                }
                return $res;
            }
            $thousands = (int) ($number / 1000);
            $remainder = $number % 1000;
            $res = convertNumberToWords($thousands) . ' ' . $dictionary[1000];
            if ($remainder) {
                $res .= ($remainder < 100 ? ' and ' : ', ') . convertNumberToWords($remainder);
            }
            return $res;
        }
    @endphp

    <div class="receipt-box">
        <div class="header">
            <h2>{{ $school->school_name }}</h2>
            <p>{{ $school->address ?? 'Institutional Address' }}<br>Mobile: {{ $school->mobile ?? 'N/A' }}</p>
        </div>

        <div class="receipt-title">MONEY RECEIPT</div>

        <table class="info-table">
            <tr>
                <td class="label">Receipt No:</td>
                <td>#INC-{{ str_pad($income->id, 5, '0', STR_PAD_LEFT) }}</td>
                <td class="label">Date:</td>
                <td>{{ date('d M, Y', strtotime($income->date)) }}</td>
            </tr>
            <tr>
                <td class="label">Received From:</td>
                <td colspan="3"><strong>{{ $income->name }}</strong></td>
            </tr>
            <tr>
                <td class="label">Address:</td>
                <td colspan="3">{{ $income->address ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td class="label">Mobile:</td>
                <td colspan="3">{{ $income->mobile }}</td>
            </tr>
            <tr>
                <td class="label">Purpose:</td>
                <td colspan="3">{{ $income->income_source }}</td>
            </tr>
        </table>

        <div class="amount-section">
            Amount Received: {{ number_format($income->amount, 2) }} Tk
        </div>

        <p style="text-transform: capitalize; font-style: italic;">
            <strong>In Words:</strong> Only {{ convertNumberToWords($income->amount) }} Taka.
        </p>

        <table class="footer-table">
            <tr>
                <td>
                    <div class="signature-space">Payer Signature</div>
                </td>
                <td>
                    <div class="signature-space">Authorized Signature</div>
                </td>
            </tr>
        </table>
    </div>

    <p style="text-align: center; font-size: 9px; color: #888; margin-top: 10px;">
        This is a computer-generated receipt. Generated on {{ date('d-m-Y H:i:s') }}
    </p>

</body>

</html>
