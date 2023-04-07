<h1>Ingredient stock alert</h1>

<p>The stock of the following ingredients has fallen below the threshold:</p>

<ul>
    {{-- @foreach ($ingredients as $ingredient ) --}}
        <li>{{ $ingredient->name }} ({{ $ingredient->stock }})</li>
    {{-- @endforeach --}}
</ul>