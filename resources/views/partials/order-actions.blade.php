<a href="{{ route('order.view', $order->id) }}"
   title="View"
   class="btn btn-sm btn-text-secondary rounded-pill btn-icon">
    <i class="mdi mdi-eye-arrow-right-outline" style="color: #0a14ad;"></i>
</a>
<a href="{{ route('order.edit', $order->id) }}"
   title="Edit"
   class="btn btn-sm btn-text-secondary rounded-pill btn-icon">
    <i class="mdi mdi-pencil-outline" style="color: #ff8000;"></i>
</a>
<a href="#"
   title="Delete"
   class="btn btn-sm btn-text-secondary rounded-pill btn-icon"
   onclick="deleteOrder({{ $order->id }})">
    <i class="mdi mdi-delete" style="color: #800000"></i>
</a>
