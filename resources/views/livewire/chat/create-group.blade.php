<form wire:submit="create" class="space-y-4">
    <div>
        <label class="block mb-1 font-bold">عنوان گروه</label>
        <input type="text" wire:model.defer="title" class="w-full rounded border px-3 py-2 focus:ring-2 focus:ring-blue-400 transition-all" required maxlength="100">
        @error('title') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
    </div>
    <div>
        <label class="block mb-1 font-bold">انتخاب اعضا</label>
        <select wire:model="users" multiple class="w-full rounded border px-3 py-2 focus:ring-2 focus:ring-blue-400 transition-all">
            @foreach($allUsers as $user)
                <option value="{{ $user->id }}">{{ $user->username }}</option>
            @endforeach
        </select>
        @error('users') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
    </div>
    <button type="submit" class="w-full bg-green-500 text-white rounded py-2 hover:bg-green-600 transition">ساخت گروه</button>
</form>
