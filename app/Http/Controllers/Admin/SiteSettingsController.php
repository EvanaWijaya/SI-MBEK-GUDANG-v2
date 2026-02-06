<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class SiteSettingsController extends Controller
{
    public function edit()
    {
        $settings = SiteSetting::first();

        return view('admin.site-settings.edit', compact('settings'));
    }

    public function update(Request $request)
    {
        $settings = SiteSetting::firstOrNew(['id' => 1]);
        // Normalisasi data social agar bisa kosong atau #
        $socialInput = $request->input('social', []);
        $normalizedSocial = [];
        foreach (['twitter', 'facebook', 'instagram', 'linkedin'] as $socialKey) {
            $active = isset($socialInput[$socialKey]['active']) ? 'on' : null;
            // Jika user isi kosong atau #, simpan apa adanya
            $url = isset($socialInput[$socialKey]['url']) ? $socialInput[$socialKey]['url'] : null;
            $normalizedSocial[$socialKey] = [
                'active' => $active,
                'url' => $url,
            ];
        }
        $validator = Validator::make($request->all(), [
            'site_name' => 'required|string|max:255',
            'site_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'hero_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',

            'sections.hero.title' => 'nullable|string|max:255',
            'sections.hero.subtitle' => 'nullable|string',
            'sections.hero.description' => 'nullable|string',
            'sections.hero.button_text' => 'nullable|string|max:100',
            'sections.hero.button_url' => 'nullable|url',

            'sections.why.heading' => 'nullable|string|max:255',
            'sections.why.items.*.title' => 'nullable|string|max:255',
            'sections.why.items.*.description' => 'nullable|string',
            'sections.why.images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',

            'sections.cta.heading' => 'nullable|string|max:255',
            'sections.cta.subheading' => 'nullable|string',
            'sections.cta.button_text' => 'nullable|string|max:100',
            'sections.cta.button_url' => 'nullable|url',

            'sections.info.title' => 'nullable|string|max:255',
            'sections.info.subtitle' => 'nullable|string',
            'sections.info.boxes.*.title' => 'nullable|string|max:255',
            'sections.info.boxes.*.content' => 'nullable|string',
        ]);

        $validator->addRules([
            'about_page.title' => 'nullable|string|max:255',
            'about_page.subtitle' => 'nullable|string|max:255',
            'about_page.sections.*.title' => 'nullable|string|max:255',
            'about_page.sections.*.content' => 'nullable|string',
            'about_page.sections.*.items.*' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $settings = SiteSetting::firstOrNew(['id' => 1]);

        // Handle site logo upload
        if ($request->hasFile('site_logo')) {
            // Hapus file lama hanya jika ada
            if (!empty($settings->site_logo)) {
                Storage::delete('public/' . $settings->site_logo);
            }
            $path = $request->file('site_logo')->store('public/settings');
            $settings->site_logo = str_replace('public/', '', $path);
        }

        // Ambil nilai awal sections
        $sections = $settings->sections;

        // Hero image
        if ($request->hasFile('hero_image')) {
            // Hapus file lama hanya jika ada
            if (!empty($sections['hero']['image'])) {
                Storage::delete('public/' . $sections['hero']['image']);
            }
            $path = $request->file('hero_image')->store('public/sections/hero');
            $sections['hero']['image'] = str_replace('public/', '', $path);
        }

        // Why Section images
        if ($request->hasFile('sections.why.images')) {
            foreach ($request->file('sections.why.images') as $idx => $file) {
                if (!$file) {
                    continue;
                }
                // Hapus file lama hanya jika ada
                if (!empty($sections['why']['images'][$idx])) {
                    Storage::delete('public/' . $sections['why']['images'][$idx]);
                }
                $path = $file->store('public/sections/why');
                $sections['why']['images'][$idx] = str_replace('public/', '', $path);
            }
        }

        // Handle About Page
        $aboutPage = [
            'active' => $request->has('about_page.active'),
            'title' => $request->input('about_page.title'),
            'subtitle' => $request->input('about_page.subtitle'),
            'sections' => [],
        ];

        foreach ($request->input('about_page.sections', []) as $section) {
            $newSection = [
                'title' => $section['title'] ?? '',
                'content' => $section['content'] ?? '',
            ];
            if (!empty($section['items'])) {
                $newSection['items'] = is_array($section['items']) ? $section['items'] : explode("\n", $section['items']);
            }
            $aboutPage['sections'][] = $newSection;
        }

        $settings->about_page = $aboutPage;

        // Merge Sections Input
        $input = $request->input('sections', []);

        $sections['hero'] = array_merge(
            [
                'active' => $request->has('sections.hero.active'),
                'title' => 'Raman Farm',
                'subtitle' => 'Pantau Pertumbuhan, Tingkatkan Produktivitas',
                'description' => 'Selamat datang di website Sistem Informasi...',
                'image' => $sections['hero']['image'] ?? null,
                'button_text' => 'Selengkapnya',
                'button_url' => '/about',
            ],
            $input['hero'] ?? [],
        );

        $sections['why'] = array_merge(
            [
                'active' => $request->has('sections.why.active'),
                'heading' => 'Mengapa Si Mbek?',
                'items' => [],
                'images' => $sections['why']['images'] ?? [],
            ],
            $input['why'] ?? [],
        );

        $sections['cta'] = array_merge(
            [
                'active' => $request->has('sections.cta.active'),
                'heading' => 'Bergabung Dengan Si Mbek',
                'subheading' => 'Mulailah perjalanan Anda ...',
                'button_text' => 'DAFTAR SEKARANG',
                'button_url' => route('login'),
            ],
            $input['cta'] ?? [],
        );

        $sections['info'] = array_merge(
            [
                'active' => $request->has('sections.info.active'),
                'title' => 'Informasi Kambing',
                'subtitle' => 'Pelajari lebih lanjut ...',
                'boxes' => [],
            ],
            $input['info'] ?? [],
        );

        // Simpan semua data
        $settings->site_name = $request->input('site_name');
        $settings->sections = $sections;
        $settings->social = $normalizedSocial; // <-- gunakan hasil normalisasi
        $settings->contact = $request->input('contact');
        $settings->map = $request->input('map');
        $settings->save();

        // dd($request->all());
        return redirect()->route('admin.site-settings.edit')->with('success', 'Pengaturan berhasil diperbarui');
    }
}
