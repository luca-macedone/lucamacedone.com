<form
    class="lg:row-span-2 flex flex-col gap-2.5 p-4 px-5 border border-muted rounded-lg order-3 lg:order-2 bg-background-contrast"
    wire:submit="submit">
    <h2 class="text-lg font-semibold text-secondary">Send Me a Message</h2>
    <p>Tell me about your project or just say hello. I'll get back to you as soon as possible.</p>
    @if (session()->has('message'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-md font-semibold">
            {{ session('message') }}
        </div>
    @endif
    <label class="flex flex-col" for="msg-contact-name">
        Name
        <input wire:model="name"
            class="border rounded-md font-mono @error('name') rounded-es-none rounded-ee-none @enderror" type="text"
            name="contact-name" id="msg-contact-name">
        @error('name')
            <span
                class="bg-red-200 text-red-400 px-3 py-1 rounded-ee-md rounded-es-md border border-t-0 border-red-400">{{ $message }}</span>
        @enderror
    </label>
    <label class="flex flex-col" for="msg-contact-email">
        Email
        <input wire:model="email"
            class="border rounded-md font-mono @error('email') rounded-es-none rounded-ee-none @enderror" type="email"
            name="contact-email" id="msg-contact-email">
        @error('email')
            <span
                class="bg-red-200 text-red-400 px-3 py-1 rounded-ee-md rounded-es-md border border-t-0 border-red-400">{{ $message }}</span>
        @enderror
    </label>
    <label class="flex flex-col" for="msg-contact-subject">
        Subject
        <input wire:model="subject"
            class="border rounded-md font-mono @error('subject') rounded-es-none rounded-ee-none @enderror"
            type="text" name="contact-subject" id="msg-contact-subject">
        @error('subject')
            <span
                class="bg-red-200 text-red-400 px-3 py-1 rounded-ee-md rounded-es-md border border-t-0 border-red-400">{{ $message }}</span>
        @enderror
    </label>
    <label class="flex flex-col" for="msg-contact-message">
        Message
        <textarea wire:model="message"
            class="border rounded-md font-mono @error('message') rounded-es-none rounded-ee-none @enderror" type="text"
            name="contact-message" id="msg-contact-message" rows="3"></textarea>
        @error('message')
            <span
                class="bg-red-200 text-red-400 px-3 py-1 rounded-ee-md rounded-es-md border border-t-0 border-red-400">{{ $message }}</span>
        @enderror
    </label>
    <div class="flex items-center gap-2.5 mt-2 ">
        <button class="border border-muted py-2 px-3 rounded-md " type="reset" wire:click="resetForm">
            Reset
        </button>
        <button
            class="border border-background py-2 px-3 rounded-md bg-primary text-background hover:bg-accent hover:border-primary hover:text-primary ease-in-out duration-200 w-full
        "
            type="submit">
            Submit
        </button>
    </div>
</form>
