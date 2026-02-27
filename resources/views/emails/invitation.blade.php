<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invitation EasyColoc</title>
</head>
<body style="margin:0;padding:24px;background:#f9fafb;font-family:Arial,sans-serif;color:#111827;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width:640px;margin:0 auto;background:#ffffff;border:1px solid #e5e7eb;border-radius:12px;overflow:hidden;">
        <tr>
            <td style="padding:24px;">
                <h1 style="margin:0 0 12px;font-size:20px;">Bonjour,</h1>

                <p style="margin:0 0 12px;font-size:15px;line-height:1.6;">
                    Vous avez recu une invitation pour rejoindre la colocation
                    <strong>{{ $invitation->colocation->name ?? 'EasyColoc' }}</strong>.
                </p>

                <p style="margin:0 0 20px;font-size:14px;color:#6b7280;">
                    Cette invitation expire le {{ $invitation->expires_at->format('d/m/Y H:i') }}.
                </p>

                <table role="presentation" cellspacing="0" cellpadding="0" style="margin-bottom:20px;">
                    <tr>
                        <td style="padding-right:10px;">
                            <a href="{{ $acceptUrl }}" style="display:inline-block;background:#6366f1;color:#ffffff;text-decoration:none;padding:10px 16px;border-radius:8px;font-size:14px;font-weight:600;">
                                Accepter l'invitation
                            </a>
                        </td>
                        <td>
                            <a href="{{ $refuseUrl }}" style="display:inline-block;background:#ef4444;color:#ffffff;text-decoration:none;padding:10px 16px;border-radius:8px;font-size:14px;font-weight:600;">
                                Refuser
                            </a>
                        </td>
                    </tr>
                </table>

                <p style="margin:0;font-size:13px;color:#6b7280;line-height:1.6;">
                    Si les boutons ne fonctionnent pas, utilisez ces liens :<br>
                    Accepter: <a href="{{ $acceptUrl }}">{{ $acceptUrl }}</a><br>
                    Refuser: <a href="{{ $refuseUrl }}">{{ $refuseUrl }}</a>
                </p>
            </td>
        </tr>
    </table>
</body>
</html>
