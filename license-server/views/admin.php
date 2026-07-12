<?php /** @var array $config @var array $rows */ ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($config['brand']) ?></title>
    <style>
        *{box-sizing:border-box} body{font-family:system-ui,Segoe UI,Roboto,sans-serif;background:#0f1420;color:#e7ebf3;margin:0}
        header{display:flex;align-items:center;justify-content:space-between;padding:16px 24px;border-bottom:1px solid #232c3f}
        header h1{font-size:16px;margin:0}
        a.logout{color:#8a94a6;font-size:13px;text-decoration:none}
        .wrap{padding:24px;max-width:1100px;margin:0 auto}
        .panel{background:#171d2b;border:1px solid #232c3f;border-radius:12px;padding:18px;margin-bottom:20px}
        .panel h2{font-size:14px;margin:0 0 14px}
        .row{display:flex;gap:10px;flex-wrap:wrap;align-items:end}
        .row .f{display:flex;flex-direction:column;gap:5px}
        label{font-size:11px;color:#8a94a6;text-transform:uppercase;letter-spacing:.05em}
        input,select{padding:9px 11px;border-radius:8px;border:1px solid #2b3448;background:#0f1420;color:#e7ebf3;font-size:13px}
        button{padding:9px 16px;border:none;border-radius:8px;background:#2f6fed;color:#fff;font-weight:600;cursor:pointer;font-size:13px}
        button.sec{background:#2b3448}
        button.warn{background:#7a2b38}
        table{width:100%;border-collapse:collapse;font-size:13px}
        th,td{text-align:left;padding:10px 8px;border-bottom:1px solid #232c3f;vertical-align:top}
        th{color:#8a94a6;font-weight:600;font-size:11px;text-transform:uppercase}
        code{background:#0f1420;padding:2px 7px;border-radius:6px;border:1px solid #2b3448;font-size:12px}
        .badge{font-size:11px;padding:2px 8px;border-radius:999px}
        .b-active{background:#173a29;color:#5fd68f} .b-revoked{background:#3a1d24;color:#ff9aa8}
        .b-item{background:#1b2b4a;color:#8ab4ff} .b-addon{background:#2e2447;color:#c3a8ff}
        .muted{color:#8a94a6}
        .acts{display:flex;gap:6px;flex-wrap:wrap}
        .copy{cursor:pointer}
        .modrow{background:#10151f}
        .modrow td{padding:14px 16px}
        .chips{display:flex;flex-wrap:wrap;gap:8px}
        .chip{display:inline-flex;align-items:center;gap:7px;font-size:12px;padding:6px 12px;border-radius:999px;cursor:pointer;border:1px solid #2b3448;user-select:none;transition:.12s}
        .chip .dot{width:8px;height:8px;border-radius:50%}
        .chip.on{background:#153726;border-color:#1f6b43;color:#7fe0a6} .chip.on .dot{background:#3fd07f}
        .chip.off{background:#1a1420;border-color:#4a2b38;color:#d98aa0} .chip.off .dot{background:#d6478e}
        .chip:hover{filter:brightness(1.15)}
        .mod-hint{font-size:11px;color:#8a94a6;margin-bottom:8px}
        .matrix{font-size:12px;border-collapse:collapse;width:100%}
        .matrix th,.matrix td{border:1px solid #232c3f;padding:6px 9px;text-align:center;white-space:nowrap}
        .matrix td.feat{text-align:left;color:#cdd3df} .matrix td.feat small{color:#8a94a6;display:block;font-size:10px}
        .mcell{cursor:pointer;font-weight:600;font-size:10px;text-transform:uppercase;letter-spacing:.03em;min-width:76px}
        .m-full{background:#153726;color:#7fe0a6} .m-fallback{background:#3a2e12;color:#e6c06a} .m-off{background:#1a2130;color:#6b7688}
        .mcell:hover{filter:brightness(1.25)}
        .grouphead td{background:#121826;color:#8a94a6;text-transform:uppercase;font-size:10px;letter-spacing:.05em;text-align:left}
        .mtx-legend{display:flex;gap:14px;font-size:11px;color:#8a94a6;margin-bottom:10px;flex-wrap:wrap}
        .mtx-legend span{display:inline-flex;align-items:center;gap:6px}
        .lg{width:12px;height:12px;border-radius:3px;display:inline-block}
    </style>
</head>
<body>
    <header>
        <h1><?= htmlspecialchars($config['brand']) ?></h1>
        <a class="logout" href="/admin/logout">Log out</a>
    </header>
    <div class="wrap">
        <div class="panel">
            <h2>Issue a new license</h2>
            <div class="row">
                <div class="f"><label>Type</label>
                    <select id="c-type">
                        <option value="item">Item (main domain)</option>
                        <option value="addon">Addon</option>
                    </select>
                </div>
                <div class="f"><label>Plan</label>
                    <select id="c-plan">
                        <?php foreach (Licenses::PLANS as $pk => $pv): ?>
                            <option value="<?= $pk ?>" <?= $pk === 'basic' ? 'selected' : '' ?>><?= htmlspecialchars($pv['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="f"><label>Addon identifier</label><input id="c-addon" placeholder="e.g. pos_system"></div>
                <div class="f"><label>Buyer name</label><input id="c-buyer"></div>
                <div class="f"><label>Buyer email</label><input id="c-email"></div>
                <div class="f"><label>Max domains</label><input id="c-max" type="number" value="<?= (int)$config['default_max_domains'] ?>" style="width:90px"></div>
                <div class="f"><label>Note</label><input id="c-note"></div>
                <button onclick="createLicense()">Generate code</button>
            </div>
        </div>

        <div class="panel">
            <h2>Plan feature matrix</h2>
            <div class="mtx-legend">
                <span><i class="lg" style="background:#153726"></i> Full — licensed</span>
                <span><i class="lg" style="background:#3a2e12"></i> Fallback — locked basic version</span>
                <span><i class="lg" style="background:#1a2130"></i> Off — hidden</span>
                <span style="color:#6b7688">Click a cell to toggle full ⇄ off. Essential features show <b style="color:#e6c06a">Fallback</b> when off.</span>
            </div>
            <div style="overflow-x:auto">
            <table class="matrix">
                <thead><tr><th style="text-align:left">Feature</th>
                    <?php foreach (Licenses::PLANS as $pk => $pv): ?><th><?= htmlspecialchars($pv['name']) ?></th><?php endforeach; ?>
                </tr></thead>
                <tbody>
                    <?php $curGroup = null; foreach (Licenses::FEATURE_CATALOG as $fk => $fdef):
                        [$flabel, $fgroup, $fmin, $fess, $ffb] = $fdef;
                        if ($fgroup !== $curGroup): $curGroup = $fgroup; ?>
                            <tr class="grouphead"><td colspan="<?= count(Licenses::PLANS) + 1 ?>"><?= htmlspecialchars($fgroup) ?></td></tr>
                        <?php endif; ?>
                        <tr>
                            <td class="feat"><?= htmlspecialchars($flabel) ?><?php if ($fess): ?><small>fallback: <?= htmlspecialchars($ffb) ?></small><?php endif; ?></td>
                            <?php foreach (array_keys(Licenses::PLANS) as $pk): $st = $planMatrix[$pk][$fk] ?? 'off'; ?>
                                <td class="mcell m-<?= $st ?>" data-plan="<?= $pk ?>" data-feature="<?= $fk ?>" data-ess="<?= $fess ? 1 : 0 ?>" onclick="cycleCell(this)"><?= $st ?></td>
                            <?php endforeach; ?>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            </div>
        </div>

        <div class="panel">
            <h2>Licenses (<?= count($rows) ?>)</h2>
            <table>
                <thead><tr>
                    <th>Code</th><th>Type</th><th>Plan</th><th>Buyer</th><th>Domains</th><th>Status</th><th>Actions</th>
                </tr></thead>
                <tbody id="lic-body">
                <?php foreach ($rows as $r): ?>
                    <tr data-code="<?= htmlspecialchars($r['code']) ?>">
                        <td><code class="copy" title="Click to copy" onclick="copyText(this)"><?= htmlspecialchars($r['code']) ?></code></td>
                        <td>
                            <span class="badge b-<?= $r['type'] ?>"><?= htmlspecialchars($r['type']) ?></span>
                            <?php if ($r['addon_identifier']): ?><div class="muted"><?= htmlspecialchars($r['addon_identifier']) ?></div><?php endif; ?>
                        </td>
                        <td>
                            <select class="planpick" data-code="<?= htmlspecialchars($r['code']) ?>" onchange="setPlan(this)">
                                <?php $cp = $r['plan'] ?? 'enterprise'; foreach (Licenses::PLANS as $pk => $pv): ?>
                                    <option value="<?= $pk ?>" <?= $cp === $pk ? 'selected' : '' ?>><?= htmlspecialchars($pv['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                        <td><?= htmlspecialchars($r['buyer_name'] ?: '—') ?><div class="muted"><?= htmlspecialchars($r['buyer_email'] ?: '') ?></div></td>
                        <td>
                            <?php if ($r['domains']): ?>
                                <?php foreach ($r['domains'] as $d): ?><div><?= htmlspecialchars($d) ?></div><?php endforeach; ?>
                            <?php else: ?><span class="muted">not activated</span><?php endif; ?>
                            <div class="muted">max <?= (int)$r['max_domains'] ?></div>
                        </td>
                        <td><span class="badge b-<?= $r['status'] ?>"><?= htmlspecialchars($r['status']) ?></span></td>
                        <td><div class="acts">
                            <button class="sec" onclick="toggleModules('<?= htmlspecialchars($r['code']) ?>')">Modules</button>
                            <?php if ($r['status'] === 'active'): ?>
                                <button class="warn" onclick="act('revoke','<?= htmlspecialchars($r['code']) ?>')">Revoke</button>
                            <?php else: ?>
                                <button class="sec" onclick="act('activate','<?= htmlspecialchars($r['code']) ?>')">Reactivate</button>
                            <?php endif; ?>
                            <button class="sec" onclick="act('reset','<?= htmlspecialchars($r['code']) ?>')">Reset domains</button>
                        </div></td>
                    </tr>
                    <tr class="modrow" id="mod-<?= htmlspecialchars($r['code']) ?>" style="display:none">
                        <td colspan="7">
                            <div class="mod-hint">Click a module to toggle its license for this code. Green = enabled, red = disabled.</div>
                            <div class="chips">
                                <?php $mods = $r['modules'] ?? []; foreach (Licenses::MODULE_CATALOG as $mid => $mlabel): $on = ($mods[$mid] ?? true); ?>
                                    <span class="chip <?= $on ? 'on' : 'off' ?>"
                                          data-code="<?= htmlspecialchars($r['code']) ?>" data-module="<?= htmlspecialchars($mid) ?>"
                                          onclick="toggleEnt(this)">
                                        <span class="dot"></span><?= htmlspecialchars($mlabel) ?>
                                    </span>
                                <?php endforeach; ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        async function api(path, data) {
            const res = await fetch(path, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data || {})
            });
            return res.json();
        }
        async function createLicense() {
            const payload = {
                type: document.getElementById('c-type').value,
                plan: document.getElementById('c-plan').value,
                addon_identifier: document.getElementById('c-addon').value.trim() || null,
                buyer_name: document.getElementById('c-buyer').value.trim(),
                buyer_email: document.getElementById('c-email').value.trim(),
                max_domains: parseInt(document.getElementById('c-max').value || '1', 10),
                note: document.getElementById('c-note').value.trim()
            };
            const r = await api('/admin/api/licenses', payload);
            if (r.created) { alert('New code: ' + r.license.code); location.reload(); }
            else alert('Failed');
        }
        async function act(kind, code) {
            if (kind === 'revoke' && !confirm('Revoke ' + code + '?')) return;
            if (kind === 'reset' && !confirm('Unbind all domains for ' + code + '?')) return;
            await api('/admin/api/licenses/' + kind, { code });
            location.reload();
        }
        function copyText(el) {
            navigator.clipboard.writeText(el.textContent).then(() => { el.style.color = '#5fd68f'; setTimeout(()=>el.style.color='',700); });
        }
        async function setPlan(sel) {
            const r = await api('/admin/api/licenses/plan', { code: sel.dataset.code, plan: sel.value });
            if (!r.ok) alert('Failed to set plan');
        }
        async function cycleCell(td) {
            const cur = td.classList.contains('m-full') ? 'full' : (td.classList.contains('m-fallback') ? 'fallback' : 'off');
            const enable = (cur !== 'full');   // anything not full -> turn on; full -> turn off
            td.style.opacity = '.5';
            const r = await api('/admin/api/plan-feature', { plan: td.dataset.plan, feature: td.dataset.feature, enabled: enable });
            td.style.opacity = '1';
            if (!r.ok) { alert('Failed'); return; }
            let next;
            if (enable) next = 'full';
            else next = td.dataset.ess === '1' ? 'fallback' : 'off';
            td.classList.remove('m-full','m-fallback','m-off');
            td.classList.add('m-' + next);
            td.textContent = next;
        }
        function toggleModules(code) {
            const row = document.getElementById('mod-' + code);
            row.style.display = row.style.display === 'none' ? 'table-row' : 'none';
        }
        async function toggleEnt(el) {
            const code = el.dataset.code, module = el.dataset.module;
            const enable = el.classList.contains('off');   // clicking an off chip enables it
            el.style.opacity = '.5';
            const r = await api('/admin/api/entitlements/toggle', { code, module, enabled: enable });
            el.style.opacity = '1';
            if (r.ok) {
                el.classList.toggle('on', enable);
                el.classList.toggle('off', !enable);
            } else {
                alert('Failed to update');
            }
        }
    </script>
</body>
</html>
