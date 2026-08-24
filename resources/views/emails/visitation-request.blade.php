<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>Visitation request</title>
    </head>
    <body style="margin: 0; padding: 24px; background: #f2ede2; color: #14261f; font-family: Arial, sans-serif;">
        <div style="max-width: 680px; margin: 0 auto; padding: 28px; border-radius: 16px; background: #ffffff;">
            <h1 style="margin-top: 0; color: #17392c;">New visitation request</h1>
            <p>A visitor submitted a coordination request through the MIBNP website.</p>

            <table style="width: 100%; border-collapse: collapse;">
                <tbody>
                    <tr>
                        <th style="padding: 8px 0; text-align: left;">Name</th>
                        <td style="padding: 8px 0;">{{ $details['name'] }}</td>
                    </tr>
                    <tr>
                        <th style="padding: 8px 0; text-align: left;">Email</th>
                        <td style="padding: 8px 0;">{{ $details['email'] }}</td>
                    </tr>
                    <tr>
                        <th style="padding: 8px 0; text-align: left;">Phone</th>
                        <td style="padding: 8px 0;">{{ $details['phone'] ?: 'Not provided' }}</td>
                    </tr>
                    <tr>
                        <th style="padding: 8px 0; text-align: left;">Organization</th>
                        <td style="padding: 8px 0;">{{ $details['organization'] ?: 'Not provided' }}</td>
                    </tr>
                    <tr>
                        <th style="padding: 8px 0; text-align: left;">Activity</th>
                        <td style="padding: 8px 0;">{{ $details['activity'] }}</td>
                    </tr>
                    <tr>
                        <th style="padding: 8px 0; text-align: left;">Intended date</th>
                        <td style="padding: 8px 0;">{{ $details['intended_date'] }}</td>
                    </tr>
                    <tr>
                        <th style="padding: 8px 0; text-align: left;">Number of visitors</th>
                        <td style="padding: 8px 0;">{{ $details['visitor_count'] }}</td>
                    </tr>
                    <tr>
                        <th style="padding: 8px 0; text-align: left;">Document type</th>
                        <td style="padding: 8px 0;">{{ $details['document_type'] ?: 'No document attached' }}</td>
                    </tr>
                </tbody>
            </table>

            <h2 style="margin-bottom: 8px; color: #17392c;">Additional information</h2>
            <p style="white-space: pre-wrap;">{{ $details['message'] ?: 'No additional information provided.' }}</p>

            <p style="margin-bottom: 0; color: #5e6f68;">
                Reply directly to this message to contact {{ $details['name'] }}.
            </p>
        </div>
    </body>
</html>
