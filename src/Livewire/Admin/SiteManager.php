<?php

namespace Uiaciel\SuryaCms\Livewire\Admin;

use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Uiaciel\SuryaCms\Models\Setting;
use Uiaciel\SuryaCms\Models\Site;
use Uiaciel\SuryaCms\Models\SiteDomain;
use Uiaciel\SuryaCms\Support\SiteManagementAccess;

class SiteManager extends Component
{
    public string $siteName = '';

    public string $primaryDomain = '';

    public ?int $domainSiteId = null;

    public string $additionalDomain = '';

    public ?int $editingSiteId = null;

    public string $editingSiteName = '';

    public function boot(SiteManagementAccess $access): void
    {
        $access->ensureAccess();
    }

    public function createSite(): void
    {
        $normalizedDomain = $this->normalizeDomain($this->primaryDomain, 'primaryDomain');
        if ($normalizedDomain === null) {
            return;
        }
        $this->primaryDomain = $normalizedDomain;

        $this->validate([
            'siteName' => ['required', 'string', 'max:255'],
            'primaryDomain' => ['required', 'string', 'max:255', Rule::unique('site_domains', 'domain')],
        ], [
            'primaryDomain.unique' => 'Domain ini sudah digunakan oleh site lain.',
        ]);

        DB::transaction(function () use ($normalizedDomain): void {
            $site = Site::query()->create(['name' => $this->siteName]);
            $site->domains()->create([
                'domain' => $normalizedDomain,
                'is_primary' => true,
            ]);

            Setting::query()->create([
                'site_id' => $site->getKey(),
                'sitename' => $site->name,
                'url' => 'https://'.$normalizedDomain,
            ]);
        });

        $this->reset(['siteName', 'primaryDomain']);
        session()->flash('success', 'Site baru berhasil dibuat.');
    }

    public function addDomain(): void
    {
        $normalizedDomain = $this->normalizeDomain($this->additionalDomain, 'additionalDomain');
        if ($normalizedDomain === null) {
            return;
        }
        $this->additionalDomain = $normalizedDomain;

        $this->validate([
            'domainSiteId' => ['required', 'integer', Rule::exists('sites', 'id')],
            'additionalDomain' => ['required', 'string', 'max:255', Rule::unique('site_domains', 'domain')],
        ], [
            'additionalDomain.unique' => 'Domain ini sudah digunakan oleh site lain.',
        ]);

        Site::query()->findOrFail($this->domainSiteId)
            ->domains()
            ->create(['domain' => $normalizedDomain]);

        $this->reset(['domainSiteId', 'additionalDomain']);
        session()->flash('success', 'Domain tambahan berhasil dipetakan.');
    }

    public function beginRename(int $siteId): void
    {
        $site = Site::query()->findOrFail($siteId);
        $this->editingSiteId = $site->getKey();
        $this->editingSiteName = $site->name;
    }

    public function renameSite(): void
    {
        $this->validate([
            'editingSiteId' => ['required', 'integer', Rule::exists('sites', 'id')],
            'editingSiteName' => ['required', 'string', 'max:255'],
        ]);

        Site::query()->findOrFail($this->editingSiteId)
            ->update(['name' => $this->editingSiteName]);

        $this->reset(['editingSiteId', 'editingSiteName']);
        session()->flash('success', 'Nama site berhasil diperbarui.');
    }

    public function setPrimaryDomain(int $domainId): void
    {
        $domain = SiteDomain::query()->findOrFail($domainId);

        DB::transaction(function () use ($domain): void {
            SiteDomain::query()
                ->where('site_id', $domain->site_id)
                ->update(['is_primary' => false]);

            $domain->update(['is_primary' => true]);
        });

        session()->flash('success', 'Domain utama site berhasil diperbarui.');
    }

    public function deleteDomain(int $domainId): void
    {
        $domain = SiteDomain::query()->findOrFail($domainId);

        if ($domain->is_primary) {
            $this->addError('domains', 'Pilih domain utama yang baru sebelum menghapus domain utama saat ini.');

            return;
        }

        if (SiteDomain::query()->where('site_id', $domain->site_id)->count() <= 1) {
            $this->addError('domains', 'Site harus memiliki setidaknya satu domain.');

            return;
        }

        $domain->delete();
        session()->flash('success', 'Domain tambahan berhasil dihapus.');
    }

    public function render()
    {
        return view('suryacms::livewire.admin.site-manager', [
            'sites' => Site::query()->with('domains')->orderBy('id')->get(),
            'adminUrlPath' => trim(config('suryacms.admin_prefix', 'admin'), '/'),
        ])->layout('suryacms::layouts.app');
    }

    private function normalizeDomain(string $value, string $field): ?string
    {
        try {
            return SiteDomain::normalize($value);
        } catch (\InvalidArgumentException) {
            $this->addError($field, 'Masukkan nama domain yang valid, misalnya example.com.');

            return null;
        }
    }
}
