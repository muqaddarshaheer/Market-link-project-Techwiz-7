@php
    $steps = ['placed' => 'Placed', 'accepted' => 'Accepted', 'ready_for_pickup' => 'Ready', 'completed' => 'Completed'];
    $orderIndex = array_search($order->status, array_keys($steps));
@endphp
<div class="stepper mb-3">
    @foreach($steps as $key => $label)
        @php $i = array_search($key, array_keys($steps)); @endphp
        <div class="step {{ in_array($order->status, ['declined','cancelled']) ? 'bad' : ($orderIndex !== false && $i <= $orderIndex ? 'done' : '') }}">{{ $label }}</div>
    @endforeach
</div>
@if(in_array($order->status, ['declined','cancelled']))
    <div class="alert alert-secondary">This order is {{ str_replace('_', ' ', $order->status) }}.</div>
@endif
