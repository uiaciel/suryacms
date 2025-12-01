<div>
    <form wire:submit.prevent="addComment">
        <input type="hidden" wire:model="post_id">
        <div class="mb-4">
            <label for="name_author" class="block text-gray-700 text-sm font-bold mb-2">Name:</label>
            <input type="text" id="name_author" wire:model.defer="name_author"
                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            @error('name_author')
                <span class="text-red-500 text-xs italic">{{ $message }}</span>
            @enderror
        </div>

        <div class="mb-4">
            <label for="comment" class="block text-gray-700 text-sm font-bold mb-2">Comment:</label>
            <textarea id="comment" wire:model.defer="comment"
                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                rows="4"></textarea>
            @error('comment')
                <span class="text-red-500 text-xs italic">{{ $message }}</span>
            @enderror
        </div>

        <button type="button" wire:click.prevent="submitComment"
            class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
            Send Comment
        </button>
    </form>
</div>
