<x-owner-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl leading-tight text-brand-orange">
            {{ __('Owner - List Penitip') }}
        </h2>
    </x-slot>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');
        
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f9fafb;
        }
        
        .table-header {
            background: linear-gradient(135deg, #fff7ed 0%, #ffedd5 100%);
        }

        .table-row:hover {
            background-color: #fff2e6;
            transition: background-color 0.3s ease;
        }

        .message-success {
            background-color: #4ade80; 
            color: white;
        }

    </style>

    <div class="container overflow-x-auto p-7 my-10">
        @if (session('success'))
            <div class="message-success p-4 rounded-lg shadow-md mb-6">
                {{ session('success') }}
            </div>
        @endif

        <table id="Penitipkambing" class="w-full text-sm text-left text-black border-2 rounded shadow-lg overflow-hidden">
            <thead class="text-xs text-black uppercase table-header">
                <tr>
                    <th scope="col" class="px-6 py-3">Profil</th>
                    <th scope="col" class="px-6 py-3">Nama</th>
                    <th scope="col" class="px-6 py-3">Email</th>
                    <th scope="col" class="px-6 py-3">ID Kambing</th>
                    <th scope="col" class="px-6 py-3">JLh Kambing</th>
                    <th scope="col" class="px-6 py-3">ID Domba</th>
                    <th scope="col" class="px-6 py-3">JLh Domba</th>
                    <th scope="col" class="px-6 py-3">Alamat</th>
                </tr>
            </thead>

             <tbody>
                @foreach ($users as $user)
                    <tr class="hover:bg-orange-50 transition">
                        
                        {{-- Profil --}}
                        <td class="px-2 py-4">
                            <img src="{{ $user->profile_picture 
                                ? asset('uploads/profilImage/' . $user->profile_picture) 
                                : asset('uploads/profilImage/default.png') }}"
                                class="h-20 w-20 object-cover rounded-full" />
                        </td>

                        {{-- Nama --}}
                        <td class="px-6 py-4">{{ $user->name }}</td>

                        {{-- Email --}}
                        <td class="px-6 py-4">{{ $user->email }}</td>

                        {{-- ID Kambing --}}
                        <td class="px-6 py-4">
                            <select class="form-select rounded-md">
                                @forelse ($user->kambings as $kb)
                                    <option value="{{ $kb->id }}">{{ $kb->id }}</option>
                                @empty
                                    <option>-</option>
                                @endforelse
                            </select>
                        </td>

                        {{-- Jumlah Kambing --}}
                        <td class="px-6 py-4">
                            {{ $user->kambings->count() }}
                        </td>

                        {{-- ID Domba --}}
                        <td class="px-6 py-4">
                            <select class="form-select rounded-md">
                                @forelse ($user->domba as $db)
                                    <option value="{{ $db->id }}">{{ $db->id }}</option>
                                @empty
                                    <option>-</option>
                                @endforelse
                            </select>
                        </td>

                        {{-- Jumlah Domba --}}
                        <td class="px-6 py-4">
                            {{ $user->domba->count() }}
                        </td>

                        {{-- Alamat --}}
                        <td class="px-6 py-4">{{ $user->alamat }}</td>

                    </tr>
                @endforeach
            </tbody>
        </table>

        {{ $users->links() }}

    </div>
</x-owner-app-layout>
