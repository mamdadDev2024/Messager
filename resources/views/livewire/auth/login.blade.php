<form wire:submit='login' class=" p-2 rounded-2xl border mt-20 bg-gray-300 max-w-xs w-full flex flex-col gap-2">
    <div class=' p-1 flex flex-col gap-3 ' >
        <label for="">ایمیل</label>
        <x-element.input wire:model='email' placeholder="please insert your email ..."/>
        @error('email')
            <span class=" p-2 bg-red-500 w-full h-14 rounded-2xl text-center items-center flex flex-col justify-center">{{$message}}</span>
        @enderror
    </div>
    <div class=' p-1 flex flex-col gap-3 ' >
        <label for="">رمز عبور</label>
        <x-element.input type='password' wire:model='password' placeholder="please insert your password ..."/>
        @error('password')
            <span class=" p-2 bg-red-500 w-full h-14 rounded-2xl text-center items-center flex flex-col justify-center">{{$message}}</span>
        @enderror
    </div>
    <x-element.button wire:loading.attr="disabled" type='submit' wire:loading.class='opacity-50'>
        ورود
    </x-element.button>
</form>
