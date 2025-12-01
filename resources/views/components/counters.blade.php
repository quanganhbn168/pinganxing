@props([
    // items: [['icon'=>'bi bi-calendar', 'to'=>10, 'suffix'=>'+', 'label'=>'NĂM KINH NGHIỆM'], ...]
    'items' => [],
    'col' => 'col-6 col-lg-3 col-md-6',   // tự đổi layout nếu cần
])

<div class="wa-counters row g-4 align-items-start">
    @foreach($items as $it)
        <div class="{{ $col }}">
            <x-counter
                :from="data_get($it,'from',0)"
                :to="data_get($it,'to',0)"
                :duration="data_get($it,'duration',1200)"
                :suffix="data_get($it,'suffix','+')"
                :prefix="data_get($it,'prefix','')"
                :label="data_get($it,'label','')"
                :icon="data_get($it,'icon')"
                :locale="data_get($it,'locale','vi-VN')"
            />
        </div>
    @endforeach
</div>
