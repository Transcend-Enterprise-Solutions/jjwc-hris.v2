<style>
    .this-button{
        background: #dd9425;
    }

    .this-button:hover{
        background: #bb7b1b;
    }
</style>

<button {{ $attributes->merge(['type' => 'submit', 'class' => 'btn this-button text-white whitespace-nowrap']) }}>
    {{ $slot }}
</button>
