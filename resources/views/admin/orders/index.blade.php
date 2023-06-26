<x-app-layout>
    @section('content')
        <div class="m-3">
            <table id="rooms" class="table table-bordered table-striped dataTable dtr-inline" >
                <thead>
                    <tr>
                        <th>order ID</th>
                        <th>Total</th>
                        <th>Customer Name</th>
                        <th>Payment Method</th>
                        <th>Payment Status</th>
                        <th>Order Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
            </table>
        </div>
        
<!-- Modal -->
<div class="modal fade" id="deleteRoomModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h1 class="modal-title fs-5" id="deleteRoomModal">Delete Room</h1>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form method="post" id="deleteForm">
            @csrf
            @method('DELETE')
        <div class="modal-body">
            Are you sure?
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary">Confirm</button>
        </div>
        </form>
      </div>
    </div>
  </div>

    @endsection
    @section('scripts')
        <script>
            // let table = new DataTable('#rooms');
            let table = $('#rooms').DataTable({
                processing: true,
                serverSide: true,
                lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],
                ajax:{
                    url: "{{ route('admin.ordersTable') }}",
                    method: 'GET',
                },
                
            });
            $('.delete-btn').on('click', function(){
                let room_id = $(this).data('room_id');
                $('#deleteForm').attr('action', '/dashboard/room/'+room_id+'/delete');
            })
        </script>
    @endsection
</x-app-layout>
