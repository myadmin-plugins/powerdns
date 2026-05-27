<?php
/**
 * dns_manager()
 *
 * @return void
 * @throws \Exception
 * @throws \SmartyException
 */
function dns_manager()
{
    page_title(_('DNS Manager'));
    function_requirements('crud_dns_manager');
    crud_dns_manager();
    // Reference DNS servers (recommended nameservers for client services).
    // Three hardcoded values — pure view layer, no business logic.
    $primary_ip   = defined('POWERDNS_HOST') ? POWERDNS_HOST : '216.158.228.164';
    $servers = [
        [
            'role'      => 'Primary',
            'role_color'=> '#10b981',
            'role_bg'   => 'rgba(16, 185, 129, 0.1)',
            'hostname'  => 'cdns1.interserver.net',
            'ip'        => $primary_ip,
        ],
        [
            'role'      => 'Secondary',
            'role_color'=> '#118fdd',
            'role_bg'   => 'rgba(17, 143, 221, 0.1)',
            'hostname'  => 'cdns2.interserver.net',
            'ip'        => '216.158.234.243',
        ],
        [
            'role'      => 'Tertiary',
            'role_color'=> '#8b5cf6',
            'role_bg'   => 'rgba(139, 92, 246, 0.1)',
            'hostname'  => 'cdns3.interserver.net',
            'ip'        => '199.231.191.75',
        ],
    ];
    $html  = '<div id="dns-recommended-servers" style="margin-top: 32px; background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; box-shadow: 0 4px 14px rgba(15, 23, 42, 0.04); overflow: hidden;">';

    // Header
    $html .= '<div style="padding: 16px 22px; border-bottom: 1px solid #e2e8f0; background: rgba(17, 143, 221, 0.02); display: flex; align-items: center; gap: 12px;">';
    $html .=     '<div style="width: 36px; height: 36px; background: rgba(17, 143, 221, 0.1); color: #118fdd; border-radius: 8px; display: flex; align-items: center; justify-content: center;">';
    $html .=         '<i class="fas fa-globe"></i>';
    $html .=     '</div>';
    $html .=     '<div style="flex-grow: 1;">';
    $html .=         '<div style="font-weight: 700; color: #1e293b; font-size: 1.05rem; line-height: 1.2;">' . _('Recommended DNS Servers') . '</div>';
    $html .=         '<div style="font-size: 0.82rem; color: #64748b; margin-top: 2px;">' . _('Use these nameservers when delegating domains to InterServer.') . '</div>';
    $html .=     '</div>';
    $html .= '</div>';

    // Body — three nameserver cards
    $html .= '<div style="padding: 20px; display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 14px;">';
    $idx = 0;
    foreach ($servers as $s) {
        $idx++;
        $html .= '<div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 16px; transition: all 0.2s ease; position: relative;" onmouseover="this.style.borderColor=\'#cbd5e1\';this.style.boxShadow=\'0 4px 12px rgba(15,23,42,0.06)\';" onmouseout="this.style.borderColor=\'#e2e8f0\';this.style.boxShadow=\'none\';">';

        // Role badge
        $html .= '<div style="display: inline-flex; align-items: center; gap: 6px; background: ' . $s['role_bg'] . '; color: ' . $s['role_color'] . '; padding: 4px 10px; border-radius: 999px; font-size: 0.72rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.04em; margin-bottom: 12px;">';
        $html .=     '<span style="width: 6px; height: 6px; background: ' . $s['role_color'] . '; border-radius: 50%;"></span>';
        $html .=     htmlspecialchars($s['role']);
        $html .= '</div>';

        // Hostname row
        $html .= '<div style="display: flex; align-items: center; justify-content: space-between; gap: 8px; margin-bottom: 8px;">';
        $html .=     '<div style="flex-grow: 1; min-width: 0;">';
        $html .=         '<div style="font-size: 0.68rem; color: #94a3b8; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 2px;">' . _('Hostname') . '</div>';
        $html .=         '<div style="font-family: \'Fira Code\', ui-monospace, monospace; font-size: 0.88rem; color: #1e293b; font-weight: 600; word-break: break-all;">' . htmlspecialchars($s['hostname']) . '</div>';
        $html .=     '</div>';
        $html .=     '<button type="button" class="dns-copy-btn" data-value="' . htmlspecialchars($s['hostname']) . '" title="' . _('Copy hostname') . '" style="flex-shrink: 0; width: 30px; height: 30px; background: #ffffff; border: 1px solid #e2e8f0; border-radius: 7px; color: #64748b; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: all 0.15s;" onmouseover="this.style.background=\'rgba(17, 143, 221, 0.1)\';this.style.color=\'#118fdd\';this.style.borderColor=\'rgba(17, 143, 221, 0.3)\';" onmouseout="this.style.background=\'#ffffff\';this.style.color=\'#64748b\';this.style.borderColor=\'#e2e8f0\';">';
        $html .=         '<i class="far fa-copy" style="font-size: 0.78rem;"></i>';
        $html .=     '</button>';
        $html .= '</div>';

        // IP row
        $html .= '<div style="display: flex; align-items: center; justify-content: space-between; gap: 8px;">';
        $html .=     '<div style="flex-grow: 1; min-width: 0;">';
        $html .=         '<div style="font-size: 0.68rem; color: #94a3b8; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 2px;">' . _('IP Address') . '</div>';
        $html .=         '<div style="font-family: \'Fira Code\', ui-monospace, monospace; font-size: 0.88rem; color: #475569; font-weight: 500;">' . htmlspecialchars($s['ip']) . '</div>';
        $html .=     '</div>';
        $html .=     '<button type="button" class="dns-copy-btn" data-value="' . htmlspecialchars($s['ip']) . '" title="' . _('Copy IP') . '" style="flex-shrink: 0; width: 30px; height: 30px; background: #ffffff; border: 1px solid #e2e8f0; border-radius: 7px; color: #64748b; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: all 0.15s;" onmouseover="this.style.background=\'rgba(17, 143, 221, 0.1)\';this.style.color=\'#118fdd\';this.style.borderColor=\'rgba(17, 143, 221, 0.3)\';" onmouseout="this.style.background=\'#ffffff\';this.style.color=\'#64748b\';this.style.borderColor=\'#e2e8f0\';">';
        $html .=         '<i class="far fa-copy" style="font-size: 0.78rem;"></i>';
        $html .=     '</button>';
        $html .= '</div>';

        $html .= '</div>';
    }
    $html .= '</div>';

    // Footer hint
    $html .= '<div style="padding: 12px 22px; background: #f8fafc; border-top: 1px solid #e2e8f0; font-size: 0.78rem; color: #64748b; display: flex; align-items: center; gap: 8px;">';
    $html .=     '<i class="fas fa-info-circle" style="color: #118fdd;"></i>';
    $html .=     _('Set all three at your registrar for the best reliability. Changes can take up to 24 hours to propagate.');
    $html .= '</div>';

    $html .= '</div>';

    // Copy-to-clipboard handler — uses document.execCommand for broad support.
    $html .= '<script>(function(){var btns=document.querySelectorAll("#dns-recommended-servers .dns-copy-btn");for(var i=0;i<btns.length;i++){btns[i].addEventListener("click",function(){var v=this.getAttribute("data-value");var t=document.createElement("input");t.value=v;document.body.appendChild(t);t.select();try{document.execCommand("copy");}catch(e){}t.remove();var ic=this.querySelector("i");var orig=ic.className;ic.className="fas fa-check";ic.style.color="#10b981";var b=this;setTimeout(function(){ic.className=orig;ic.style.color="";},1500);});}})();</script>';

    add_output($html);
}
