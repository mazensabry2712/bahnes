<!doctype html>
<html lang="{{ $client->preferred_locale === 'ar' ? 'ar' : 'en' }}" dir="{{ $client->preferred_locale === 'ar' ? 'rtl' : 'ltr' }}">
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"></head>
<body style="margin:0;background:#f4f6f8;color:#172033;font-family:Arial,sans-serif;line-height:1.65;padding:24px">
    <main style="max-width:680px;margin:0 auto;background:#fff;border:1px solid #e2e6ed;border-radius:12px;padding:28px">
        <p style="font-size:12px;letter-spacing:.12em;font-weight:bold;color:#667085">BAHNES IT SOLUTIONS</p>
        <h1 style="font-size:24px;margin-bottom:8px">Project progress update</h1>
        <p>Hello {{ $client->name }},</p>
        <p>Here is the latest approved update for <strong>{{ $project->title }}</strong>.</p>
        <section style="background:#f8fafc;border-radius:8px;padding:16px;margin:20px 0">
            <p style="margin:0 0 8px"><strong>Summary</strong></p>
            <p style="margin:0;white-space:pre-line">{{ $update->summary }}</p>
        </section>
        @if ($update->completed_work)
            <h2 style="font-size:17px">Completed</h2>
            <p style="white-space:pre-line">{{ $update->completed_work }}</p>
        @endif
        @if ($update->current_work)
            <h2 style="font-size:17px">In progress</h2>
            <p style="white-space:pre-line">{{ $update->current_work }}</p>
        @endif
        @if ($update->next_step)
            <h2 style="font-size:17px">Next step</h2>
            <p style="white-space:pre-line">{{ $update->next_step }}</p>
        @endif
        @if ($update->target_date)
            <p><strong>Expected target date:</strong> {{ $update->target_date->format('M j, Y') }}</p>
        @endif
        <p style="margin-top:28px">If you have questions, reply to this email and our team will follow up.</p>
        <p>Regards,<br>BAHNES IT SOLUTIONS</p>
    </main>
</body>
</html>
