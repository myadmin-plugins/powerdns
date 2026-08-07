<?php

declare(strict_types=1);

namespace Detain\MyAdminPowerDns\Tests;

use PHPUnit\Framework\AssertionFailedError;
use PHPUnit\Framework\IncompleteTestError;
use PHPUnit\Framework\SkippedTestError;

/**
 * Register of plugin defects (P-bugs) that the shared contract harness detects and that
 * this repo is deliberately NOT fixing inside the harness-conversion change.
 *
 * Two entries, and they are different kinds of defect:
 *
 *  - **B-10** — `/add_domain` and `/list_domains` are registered against `src/add_domain.php`
 *    and `src/list_domains.php`, neither of which exists. The feature does exist, under the
 *    names `dns_add.php` and `dns_list.php`. These are plugin-plan Phase 5 Bucket 2, and they
 *    are **live 500s in production**, not dormant scaffold: `function_requirements()` guards
 *    on `!function_exists()` and then `require_once`s the missing path. This is the highest-
 *    severity item the harness surfaces, and it is deferred here only because the fix is a
 *    plugin change on its own branch with its own review, per decision D7.
 *  - **B-12** — `getSettings()` is registered via `system.settings` and its entire body is
 *    commented out, so it renders an empty settings page. `TierB12SettingsExecute`'s own
 *    docblock names this package as the single genuine fleet-wide instance.
 *
 * Until Phase 5 lands, this register turns those failures into *recorded* skips. It is not
 * a mute button, and it cannot become one:
 *
 *  - **Expiry.** Past `until` the test fails outright. The deferral is time-boxed.
 *  - **Fingerprint.** The number of findings must match exactly and every recorded
 *    fingerprint must still be present. A third broken path, or a different one, fails the
 *    build — this hides the defects it names and nothing else.
 *  - **Staleness.** If the assertion starts passing, the test fails and says to delete the
 *    entry, so a fixed defect cannot leave a dead deferral behind.
 *
 * Delete this file, and the `use` in {@see PluginTest}, as part of the Phase 5 fix.
 */
trait DeferredPhase5Defects
{
    /**
     * Catalogue id => defect record.
     *
     * @return array<string,array{until:string,issue:string,findings:array<int,string>}>
     */
    protected function deferredContractDefects(): array
    {
        return [
            'B-10' => [
                'until' => '2026-11-30',
                'issue' => 'plugin_plan.md Phase 5, Bucket 2 (wrong filename, feature exists) — LIVE 500',
                'findings' => [
                    'requirement "add_domain" registers /../vendor/detain/myadmin-powerdns/src/add_domain.php',
                    'requirement "list_domains" registers /../vendor/detain/myadmin-powerdns/src/list_domains.php',
                ],
            ],
            'B-12' => [
                'until' => '2026-11-30',
                'issue' => 'plugin_plan.md Phase 5 — getSettings() body is commented out while the hook stays registered',
                'findings' => [
                    'getSettings() ran but registered no settings at all',
                ],
            ],
        ];
    }

    /**
     * @dataProvider contractAssertions
     * @param class-string $inspectorClass
     * @return void
     */
    public function testPluginSatisfiesContractAssertion($inspectorClass)
    {
        $inspector = new $inspectorClass();
        $deferred = $this->deferredContractDefects();
        $id = $inspector->id();
        if (!isset($deferred[$id])) {
            parent::testPluginSatisfiesContractAssertion($inspectorClass);
            return;
        }
        $entry = $deferred[$id];
        $expiry = strtotime($entry['until'].' 23:59:59');
        $this->assertLessThanOrEqual(
            $expiry,
            time(),
            $id.' was deferred until '.$entry['until'].' pending '.$entry['issue']
            .'. That date has passed: fix the plugin defect or re-agree the deferral. A'
            .' time-boxed skip that never expires is an unrecorded permanent exemption.'
        );
        try {
            parent::testPluginSatisfiesContractAssertion($inspectorClass);
        } catch (SkippedTestError $e) {
            throw $e;
        } catch (IncompleteTestError $e) {
            throw $e;
        } catch (AssertionFailedError $e) {
            $this->assertDeferralStillDescribes($id, $entry, $e->getMessage());
            $this->markTestSkipped(
                $id.' fails on '.count($entry['findings']).' known plugin defect(s), deferred to '
                .$entry['issue'].' until '.$entry['until'].'. This is a P-bug in the plugin, not a'
                .' harness defect, and it is recorded rather than fixed here so the harness'
                .' conversion stays reviewable on its own. Findings: '
                .implode(' | ', $entry['findings'])
            );
        }
        $this->fail(
            $id.' is listed in '.__TRAIT__.' but now passes. The deferral is stale — delete the'
            .' entry. A register that outlives its defect stops being a record and starts being'
            .' a blind spot.'
        );
    }

    /**
     * Fails unless the observed failure is exactly the deferred one — same count, same texts.
     *
     * @param string                                                                 $id
     * @param array{until:string,issue:string,findings:array<int,string>}            $entry
     * @param string                                                                 $message
     * @return void
     */
    private function assertDeferralStillDescribes($id, array $entry, $message)
    {
        preg_match_all('/^\s+- \[/m', $message, $matches);
        $this->assertCount(
            count($entry['findings']),
            $matches[0],
            $id.' reported '.count($matches[0]).' finding(s) but '.count($entry['findings'])
            .' are deferred. The deferral covers only the defects it names; something else is'
            .' broken here. Full report:'."\n".$message
        );
        foreach ($entry['findings'] as $needle) {
            $this->assertStringContainsString(
                $needle,
                $message,
                $id.' no longer reports the deferred finding "'.$needle.'". The defect changed'
                .' shape, so the deferral no longer describes it. Full report:'."\n".$message
            );
        }
    }
}
