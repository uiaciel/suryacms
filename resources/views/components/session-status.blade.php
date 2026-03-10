{{-- Alerts --}}
<div class="space-y-4 mb-6" x-data="{ show: true }" x-show="show">
    @if (session('success'))
    <div class="flex items-center p-4 text-green-800 border-t-4 border-green-300 bg-green-50 rounded-lg" role="alert">
        <i class="fas fa-check-circle mr-3"></i>
        <div class="text-sm font-medium">
            {{ session('success') }}
        </div>
        <button type="button" @click="show = false" class="ml-auto -mx-1.5 -my-1.5 bg-green-50 text-green-500 rounded-lg focus:ring-2 focus:ring-green-400 p-1.5 hover:bg-green-200 inline-flex h-8 w-8 items-center justify-center">
            <i class="fas fa-times"></i>
        </button>
    </div>
    @endif

    @if (session('error'))
    <div class="flex items-center p-4 text-red-800 border-t-4 border-red-300 bg-red-50 rounded-lg" role="alert">
        <i class="fas fa-exclamation-circle mr-3"></i>
        <div class="text-sm font-medium">
            {{ session('error') }}
        </div>
        <button type="button" @click="show = false" class="ml-auto -mx-1.5 -my-1.5 bg-red-50 text-red-500 rounded-lg focus:ring-2 focus:ring-red-400 p-1.5 hover:bg-red-200 inline-flex h-8 w-8 items-center justify-center">
            <i class="fas fa-times"></i>
        </button>
    </div>
    @endif

    @if (session('info'))
    <div class="flex items-center p-4 text-blue-800 border-t-4 border-blue-300 bg-blue-50 rounded-lg" role="alert">
        <i class="fas fa-info-circle mr-3"></i>
        <div class="text-sm font-medium">
            {{ session('info') }}
        </div>
        <button type="button" @click="show = false" class="ml-auto -mx-1.5 -my-1.5 bg-blue-50 text-blue-500 rounded-lg focus:ring-2 focus:ring-blue-400 p-1.5 hover:bg-blue-200 inline-flex h-8 w-8 items-center justify-center">
            <i class="fas fa-times"></i>
        </button>
    </div>
    @endif

    @if (session('warning'))
    <div class="flex items-center p-4 text-yellow-800 border-t-4 border-yellow-300 bg-yellow-50 rounded-lg" role="alert">
        <i class="fas fa-exclamation-triangle mr-3"></i>
        <div class="text-sm font-medium">
            {{ session('warning') }}
        </div>
        <button type="button" @click="show = false" class="ml-auto -mx-1.5 -my-1.5 bg-yellow-50 text-yellow-500 rounded-lg focus:ring-2 focus:ring-yellow-400 p-1.5 hover:bg-yellow-200 inline-flex h-8 w-8 items-center justify-center">
            <i class="fas fa-times"></i>
        </button>
    </div>
    @endif

    @if ($errors->any())
    <div class="p-4 text-orange-800 border-t-4 border-orange-300 bg-orange-50 rounded-lg" role="alert">
        <div class="flex items-center mb-2">
            <i class="fas fa-exclamation-triangle mr-3"></i>
            <span class="text-sm font-bold">Validation Error!</span>
            <button type="button" @click="show = false" class="ml-auto -mx-1.5 -my-1.5 bg-orange-50 text-orange-500 rounded-lg focus:ring-2 focus:ring-orange-400 p-1.5 hover:bg-orange-200 inline-flex h-8 w-8 items-center justify-center">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <ul class="mt-1.5 ml-4 list-disc list-inside text-sm">
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif
</div>
