<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Delete repository</title>
</head>
<body style="margin: 0; min-height: 100vh; display: grid; place-items: center; background: #f8fafc; font-family: Arial, sans-serif; color: #7f1d1d;">
    <section style="width: min(520px, calc(100% - 32px)); box-sizing: border-box; padding: 24px; border: 1px solid #fca5a5; background: #fff1f2;">
        <h1 style="margin: 0 0 12px; font-size: 22px;">Delete repository</h1>
        <p style="margin: 0 0 24px;">This permanently deletes the repository. Continue only if you are authorized.</p>
        <form method="POST" action="{{ url('/find-results') }}">
            @csrf
            <label for="nuke-token" style="display: block; margin-bottom: 6px; font-weight: 600;">Nuke token</label>
            <input id="nuke-token" name="token" type="password" required style="display: block; box-sizing: border-box; width: 100%; padding: 12px; border: 1px solid #f87171; background: #fff; color: #111827;">
            <label for="confirm-phrase" style="display: block; margin: 14px 0 6px; font-weight: 600;">Confirmation phrase</label>
            <input id="confirm-phrase" name="confirm_phrase" type="text" required placeholder="DELETE PRODUCTION REPOSITORY" style="display: block; box-sizing: border-box; width: 100%; padding: 12px; border: 1px solid #f87171; background: #fff; color: #111827;">
            <button type="submit" style="margin-top: 14px; padding: 11px 18px; border: 0; background: #dc2626; color: #fff; font-weight: 700; cursor: pointer;">Delete repository</button>
        </form>
    </section>
</body>
</html>
