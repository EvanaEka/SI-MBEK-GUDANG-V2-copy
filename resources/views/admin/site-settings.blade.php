<form method="POST" action="{{ route('admin.site-settings.update') }}" enctype="multipart/form-data">
@csrf
@method('PUT')

<!-- Header Settings -->
<div class="mb-8 p-6 bg-white rounded-lg shadow">
    <h2 class="text-2xl font-bold mb-4">Header Settings</h2>
    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Logo Upload -->
        <div>
            <label class="block mb-2 font-medium">Site Logo</label>
            <input type="file" name="site_logo" class="block w-full text-sm text-gray-500
                  file:mr-4 file:py-2 file:px-4
                  file:rounded-full file:border-0
                  file:text-sm file:font-semibold
                  file:bg-orange-50 file:text-orange-700
                  hover:file:bg-orange-100">
            @if($settings->site_logo)
            <div class="mt-2">
                <img src="{{ asset($settings->site_logo) }}" class="w-20 h-20 object-contain" alt="Current Logo">
            </div>
            @endif
        </div>

        <!-- Site Name -->
        <div>
            <label class="block mb-2 font-medium">Site Name</label>
            <input type="text" name="site_name" value="{{ $settings->site_name }}" 
                   class="w-full px-4 py-2 border rounded-lg focus:ring-orange-500 focus:border-orange-500">
        </div>
    </div>
</div>

<!-- Content Sections -->
<div class="mb-8 p-6 bg-white rounded-lg shadow">
    <h2 class="text-2xl font-bold mb-4">Content Sections</h2>

    <!-- Hero Section -->
    <div class="mb-6 p-4 border rounded-lg">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold">Hero Section</h3>
            <label class="switch">
                <input type="checkbox" name="sections[hero][active]" {{ $settings->sections['hero']['active'] ? 'checked' : '' }}>
                <span class="slider round"></span>
            </label>
        </div>
        
        <div class="space-y-4">
            <div>
                <label>Title</label>
                <input type="text" name="sections[hero][title]" value="{{ $settings->sections['hero']['title'] }}"
                       class="w-full px-4 py-2 border rounded-lg">
            </div>
            <div>
                <label>Subtitle</label>
                <textarea name="sections[hero][subtitle]" 
                          class="w-full px-4 py-2 border rounded-lg">{{ $settings->sections['hero']['subtitle'] }}</textarea>
            </div>
             <div>
                <label>Description</label>
                <textarea name="sections[hero][subtitle]" 
                          class="w-full px-4 py-2 border rounded-lg">{{ $settings->sections['hero']['description'] }}</textarea>
            </div>
            <div>
                <label>Image</label>
                <input type="file" name="hero_image" class="block w-full text-sm text-gray-500">
            </div>
        </div>
    </div>

    <!-- Repeat similar structure for Why, CTA, and Information sections -->
    <!-- Add toggle switches and respective fields for each section -->
</div>

<!-- Footer Settings -->
<div class="mb-8 p-6 bg-white rounded-lg shadow">
    <h2 class="text-2xl font-bold mb-4">Footer Settings</h2>

    <div class="mb-6">
        <h3 class="text-lg font-semibold mb-3">Social Media</h3>
        <div class="space-y-2">
            @foreach(['twitter', 'facebook', 'instagram'] as $social)
            <div class="flex items-center gap-2">
                <label class="switch">
                    <input type="checkbox" name="social[{{ $social }}][active]" 
                           {{ $settings->social[$social]['active'] ? 'checked' : '' }}>
                    <span class="slider round"></span>
                </label>
                <span class="capitalize">{{ $social }}</span>
                <input type="url" name="social[{{ $social }}][url]" 
                       value="{{ $settings->social[$social]['url'] }}"
                       class="flex-1 px-2 py-1 border rounded">
            </div>
            @endforeach
        </div>
    </div>

    <div class="mb-6">
        <h3 class="text-lg font-semibold mb-3">Contact Information</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label>Address</label>
                <textarea name="contact[address]" 
                          class="w-full px-4 py-2 border rounded-lg">{{ $settings->contact['address'] }}</textarea>
            </div>
            <div>
                <label>Phone Number</label>
                <input type="text" name="contact[phone]" value="{{ $settings->contact['phone'] }}"
                       class="w-full px-4 py-2 border rounded-lg">
            </div>
            <div>
                <label>Email</label>
                <input type="email" name="contact[email]" value="{{ $settings->contact['email'] }}"
                       class="w-full px-4 py-2 border rounded-lg">
            </div>
        </div>
    </div>

    <div class="mb-6">
        <div class="flex justify-between items-center mb-3">
            <h3 class="text-lg font-semibold">Map Embed</h3>
            <label class="switch">
                <input type="checkbox" name="map[active]" {{ $settings->map['active'] ? 'checked' : '' }}>
                <span class="slider round"></span>
            </label>
        </div>
        <textarea name="map[embed_code]" placeholder="Paste kode <iframe> dari Google Maps di sini..."
                  class="w-full px-4 py-2 border rounded-lg h-32">{{ $settings->map['embed_code'] }}</textarea>
    </div>
</div>

<div class="text-right">
    <button type="submit" 
            class="px-6 py-2 bg-orange-500 text-white rounded-lg hover:bg-orange-600 transition-colors">
        Save Settings
    </button>
</div>
</form>

<style>
.switch {
  position: relative;
  display: inline-block;
  width: 60px;
  height: 34px;
}

.switch input { 
  opacity: 0;
  width: 0;
  height: 0;
}

.slider {
  position: absolute;
  cursor: pointer;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: #ccc;
  transition: .4s;
}

.slider:before {
  position: absolute;
  content: "";
  height: 26px;
  width: 26px;
  left: 4px;
  bottom: 4px;
  background-color: white;
  transition: .4s;
}

input:checked + .slider {
  background-color: #f97316;
}

input:checked + .slider:before {
  transform: translateX(26px);
}

.slider.round {
  border-radius: 34px;
}

.slider.round:before {
  border-radius: 50%;
}
</style>