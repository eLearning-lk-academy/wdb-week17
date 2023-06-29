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
    @push('css')
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.12/dist/sweetalert2.min.css">
    @endpush
    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.12/dist/sweetalert2.all.min.js"></script>
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
            $(document).on('click', '.cancel-btn', function(){
                let id = $(this).data('id');
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, cancel it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ url('dashboard/orders') }}/"+id+'/cancel',
                            method: 'PUT',
                            data: {
                                _token: "{{ csrf_token() }}"
                            },
                            success: function(data){
                                Swal.fire(
                                    'Cancelled!',
                                    'Order has been cancelled.',
                                    'success'
                                )
                                table.draw();
                            }
                        })
                    }
                })
            });
            $(document).on('click', '.approve-btn', function(){
                let id = $(this).data('id');
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You want to approve this order!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',

                    confirmButtonText: 'Yes, approve it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ url('dashboard/orders') }}/"+id+'/approve',
                            method: 'PUT',
                            data: {
                                _token: "{{ csrf_token() }}"
                            },
                            success: function(data){
                                Swal.fire(
                                    'Approved!',
                                    'Order has been approved.',
                                    'success'
                                )
                                table.draw();
                            },
                            error: function(data){
                                Swal.fire(
                                    'Error!',
                                    'Something went wrong.',
                                    'error'
                                )
                            }
                        })
                    }
                })
            });
            
        </script>
    @endpush
</x-app-layout>
