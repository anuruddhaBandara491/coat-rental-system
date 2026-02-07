@switch($order->status)
    @case(1)
        <div class="col">
            <span class="badge bg-warning" id="statusBadge{{ $order->id }}">Pending</span>
            <label class="switch">
                <input type="checkbox" name="status" id="statusToggle{{ $order->id }}"
                       data-current-status="1" data-order-id="{{ $order->id }}"
                       onchange="updateStatus(this)">
                <span class="slider round"></span>
            </label>
        </div>
        @break
    @case(2)
        <div class="col">
            <span class="badge bg-info" id="statusBadge{{ $order->id }}">In Use</span>
            <label class="switch">
                <input type="checkbox" name="status" id="statusToggle{{ $order->id }}"
                       data-current-status="2" data-order-id="{{ $order->id }}"
                       onchange="updateStatus(this)">
                <span class="slider round"></span>
            </label>
        </div>
        @break
    @case(3)
        <div class="col">
            <span class="badge bg-success" id="statusBadge{{ $order->id }}">Returned</span>
            <label class="switch">
                <input type="checkbox" name="status" id="statusToggle{{ $order->id }}"
                       data-current-status="3" data-order-id="{{ $order->id }}"
                       onchange="updateStatus(this)">
                <span class="slider round"></span>
            </label>
        </div>
        @break
    @case(4)
        <div class="col">
            <span class="badge bg-secondary" id="statusBadge{{ $order->id }}">Cancelled</span>
            <label class="switch">
                <input type="checkbox" name="status" id="statusToggle{{ $order->id }}"
                       data-current-status="4" data-order-id="{{ $order->id }}"
                       onchange="updateStatus(this)">
                <span class="slider round"></span>
            </label>
        </div>
@endswitch
