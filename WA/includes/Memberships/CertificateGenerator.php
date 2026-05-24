<?php

namespace WSHC\Memberships;

use Dompdf\Dompdf;
use Dompdf\Options;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;

/**
 * Generate professional monochromatic membership certificates.
 */
class CertificateGenerator {
    /**
     * Generate PDF certificate for a user.
     *
     * @param int $user_id
     * @return string PDF binary data.
     */
    public static function generate($user_id) {
        $user = get_userdata($user_id);
        if (!$user) return '';

        $membership_id = get_user_meta($user_id, 'wshc_membership_id', true);
        $expiry = get_user_meta($user_id, 'wshc_membership_expiry', true);

        // Fetch application for academic check
        global $wpdb;
        $app = $wpdb->get_row($wpdb->prepare(
            "SELECT degree, full_name FROM {$wpdb->prefix}wshc_membership_applications WHERE user_id = %d AND status = 'approved' ORDER BY created_at DESC LIMIT 1",
            $user_id
        ));

        $display_name = $app ? $app->full_name : $user->display_name;
        if ($app && strpos(strtolower($app->degree), 'ph.d') !== false) {
            $display_name = 'Dr. ' . $display_name;
        }

        $qr_data = home_url("/verify-membership/?id=" . $membership_id);
        $qr_options = new QROptions([
            'version'    => 5,
            'outputType' => QRCode::OUTPUT_MARKUP_SVG,
            'eccLevel'   => QRCode::ECC_L,
        ]);
        $qrcode = (new QRCode($qr_options))->render($qr_data);

        $html = self::get_template_html($display_name, $membership_id, $expiry, $qrcode);

        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        return $dompdf->output();
    }

    private static function get_template_html($name, $id, $expiry, $qr) {
        $date_issued = date('M d, Y');
        $expiry_formatted = date('M d, Y', strtotime($expiry));

        return "
        <html>
        <head>
            <style>
                @page { margin: 0; }
                body { font-family: 'Helvetica', sans-serif; margin: 0; padding: 0; color: #000; background: #fff; }
                .cert-container { width: 100%; height: 100%; position: relative; border: 20px solid #000; box-sizing: border-box; }
                .header { text-align: center; margin-top: 80px; }
                .logo-placeholder { font-weight: 900; font-size: 24px; letter-spacing: 2px; text-transform: uppercase; margin-bottom: 10px; }
                .sub-header { font-size: 14px; font-weight: 400; text-transform: uppercase; letter-spacing: 4px; border-top: 1px solid #000; display: inline-block; padding-top: 10px; }
                .title-block { text-align: center; margin-top: 100px; }
                .cert-title { font-size: 48px; font-weight: 900; text-transform: uppercase; margin-bottom: 20px; }
                .presented-to { font-style: italic; font-size: 18px; margin-bottom: 40px; }
                .member-name { font-size: 36px; font-weight: 800; text-transform: uppercase; border-bottom: 2px solid #000; display: inline-block; padding: 0 40px 10px; margin-bottom: 50px; }
                .statement { max-width: 500px; margin: 0 auto; line-height: 1.6; font-size: 14px; }
                .footer { position: absolute; bottom: 80px; width: 100%; padding: 0 80px; box-sizing: border-box; }
                .footer-grid { display: table; width: 100%; }
                .footer-col { display: table-cell; width: 33%; vertical-align: bottom; }
                .qr-wrap svg { width: 80px; height: 80px; }
                .meta-label { font-size: 10px; font-weight: 800; color: #666; text-transform: uppercase; margin-bottom: 4px; }
                .meta-value { font-size: 14px; font-weight: 700; }
                .sig-line { border-top: 1px solid #000; width: 150px; margin-top: 40px; text-align: center; font-size: 10px; font-weight: 800; padding-top: 5px; }
            </style>
        </head>
        <body>
            <div class='cert-container'>
                <div class='header'>
                    <div class='logo-placeholder'>Global Council of Sport Health</div>
                    <div class='sub-header'>Institutional Management System</div>
                </div>

                <div class='title-block'>
                    <div class='cert-title'>Certificate of Membership</div>
                    <div class='presented-to'>This is to officially certify that</div>
                    <div class='member-name'>$name</div>
                    <div class='statement'>
                        Is a duly recognized and accredited member of the Global Council of Sport Health,
                        having met all institutional requirements and professional standards set forth by the Council.
                    </div>
                </div>

                <div class='footer'>
                    <div class='footer-grid'>
                        <div class='footer-col' style='text-align: left;'>
                            <div class='qr-wrap'>$qr</div>
                            <div style='margin-top: 10px;'>
                                <div class='meta-label'>Verify Document</div>
                                <div class='meta-value' style='font-size: 9px;'>$id</div>
                            </div>
                        </div>
                        <div class='footer-col' style='text-align: center;'>
                             <div class='meta-label'>Issued On</div>
                             <div class='meta-value'>$date_issued</div>
                             <div style='margin-top: 20px;'>
                                 <div class='meta-label'>Valid Until</div>
                                 <div class='meta-value'>$expiry_formatted</div>
                             </div>
                        </div>
                        <div class='footer-col' style='text-align: right;'>
                            <div class='sig-line'>SECRETARY GENERAL</div>
                            <div class='sig-line' style='margin-top: 20px;'>REGIONAL COORDINATOR</div>
                        </div>
                    </div>
                </div>
            </div>
        </body>
        </html>
        ";
    }
}
