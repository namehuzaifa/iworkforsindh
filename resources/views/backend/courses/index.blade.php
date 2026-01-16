@extends('backend.layouts.app')

@section('content')
    
    <div class="container">
       
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="mb-0">Courses</h2>

            <a href="{{ route('courses.index') }}"
            target="_blank"
            class="fw-semibold text-decoration-none view">
                View Active Courses
            </a>
        </div>

        <table id="regionTable" class="table table-bordered table-striped">

            <thead>
                <tr>
                    <th>Image</th>
                    <th>Title</th>
                    <th>Category</th>
                    <th>Price</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($courses as $course)
                    <tr>
                        <td><img src="{{ asset($course->thumbnail_url) }}" class="card-img-top labor-image" alt="{{ $course->thumbnail_url }}"></td>
                        <td>{{ $course->title }}</td>
                        <td>{{ $course->category->name }}</td>
                        <td> Rs <span class="old-price" style="text-decoration: line-through;">{{ $course->price }}</span>
                            <span class="free">{{ $course->discounted_price }}</span>
                        </td>
                      
                        <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                            <a href="javascript:void(0)" class="active-status">
                                <label class="switch">
                                    <input 
                                        type="checkbox"
                                        data-id="{{ $course->id }}"
                                        class="success status-switch change-course-status"
                                        {{ $course->is_active ? 'checked' : '' }}
                                    >
                                    <span class="slider round"></span>
                                </label>

                                <p class="{{ $course->is_active ? 'active' : '' }}"
                                id="status_{{ $course->id }}">
                                    {{ $course->is_active ? 'Active' : 'Inactive' }}
                                </p>
                            </a>

                            <p class="text-xs text-gray-400">
                                Created at {{ $course->created_at->format('d M Y') }}
                            </p>
                        </td>

                        <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-6">
                            {{-- <a href="{{ route('courses.edit', $course) }}" class="text-indigo-600 hover:text-indigo-900 mr-4">Edit</a> --}}
                            <form action="{{ route('admin.courses.destroy', $course) }}" method="POST"
                                class="inline-block" onsubmit="return confirm('Are you sure?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    class="text-red-600 hover:text-red-900 bg-transparent border-0 p-0 focus:outline-none focus:ring-0">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 100 100">
                                        <path fill="#f37e98" d="M25,30l3.645,47.383C28.845,79.988,31.017,82,33.63,82h32.74c2.613,0,4.785-2.012,4.985-4.617L75,30"></path><path fill="#f15b6c" d="M65 38v35c0 1.65-1.35 3-3 3s-3-1.35-3-3V38c0-1.65 1.35-3 3-3S65 36.35 65 38zM53 38v35c0 1.65-1.35 3-3 3s-3-1.35-3-3V38c0-1.65 1.35-3 3-3S53 36.35 53 38zM41 38v35c0 1.65-1.35 3-3 3s-3-1.35-3-3V38c0-1.65 1.35-3 3-3S41 36.35 41 38zM77 24h-4l-1.835-3.058C70.442 19.737 69.14 19 67.735 19h-35.47c-1.405 0-2.707.737-3.43 1.942L27 24h-4c-1.657 0-3 1.343-3 3s1.343 3 3 3h54c1.657 0 3-1.343 3-3S78.657 24 77 24z"></path><path fill="#1f212b" d="M66.37 83H33.63c-3.116 0-5.744-2.434-5.982-5.54l-3.645-47.383 1.994-.154 3.645 47.384C29.801 79.378 31.553 81 33.63 81H66.37c2.077 0 3.829-1.622 3.988-3.692l3.645-47.385 1.994.154-3.645 47.384C72.113 80.566 69.485 83 66.37 83zM56 20c-.552 0-1-.447-1-1v-3c0-.552-.449-1-1-1h-8c-.551 0-1 .448-1 1v3c0 .553-.448 1-1 1s-1-.447-1-1v-3c0-1.654 1.346-3 3-3h8c1.654 0 3 1.346 3 3v3C57 19.553 56.552 20 56 20z"></path><path fill="#1f212b" d="M77,31H23c-2.206,0-4-1.794-4-4s1.794-4,4-4h3.434l1.543-2.572C28.875,18.931,30.518,18,32.265,18h35.471c1.747,0,3.389,0.931,4.287,2.428L73.566,23H77c2.206,0,4,1.794,4,4S79.206,31,77,31z M23,25c-1.103,0-2,0.897-2,2s0.897,2,2,2h54c1.103,0,2-0.897,2-2s-0.897-2-2-2h-4c-0.351,0-0.677-0.185-0.857-0.485l-1.835-3.058C69.769,20.559,68.783,20,67.735,20H32.265c-1.048,0-2.033,0.559-2.572,1.457l-1.835,3.058C27.677,24.815,27.351,25,27,25H23z"></path><path fill="#1f212b" d="M61.5 25h-36c-.276 0-.5-.224-.5-.5s.224-.5.5-.5h36c.276 0 .5.224.5.5S61.776 25 61.5 25zM73.5 25h-5c-.276 0-.5-.224-.5-.5s.224-.5.5-.5h5c.276 0 .5.224.5.5S73.776 25 73.5 25zM66.5 25h-2c-.276 0-.5-.224-.5-.5s.224-.5.5-.5h2c.276 0 .5.224.5.5S66.776 25 66.5 25zM50 76c-1.654 0-3-1.346-3-3V38c0-1.654 1.346-3 3-3s3 1.346 3 3v25.5c0 .276-.224.5-.5.5S52 63.776 52 63.5V38c0-1.103-.897-2-2-2s-2 .897-2 2v35c0 1.103.897 2 2 2s2-.897 2-2v-3.5c0-.276.224-.5.5-.5s.5.224.5.5V73C53 74.654 51.654 76 50 76zM62 76c-1.654 0-3-1.346-3-3V47.5c0-.276.224-.5.5-.5s.5.224.5.5V73c0 1.103.897 2 2 2s2-.897 2-2V38c0-1.103-.897-2-2-2s-2 .897-2 2v1.5c0 .276-.224.5-.5.5S59 39.776 59 39.5V38c0-1.654 1.346-3 3-3s3 1.346 3 3v35C65 74.654 63.654 76 62 76z"></path><path fill="#1f212b" d="M59.5 45c-.276 0-.5-.224-.5-.5v-2c0-.276.224-.5.5-.5s.5.224.5.5v2C60 44.776 59.776 45 59.5 45zM38 76c-1.654 0-3-1.346-3-3V38c0-1.654 1.346-3 3-3s3 1.346 3 3v35C41 74.654 39.654 76 38 76zM38 36c-1.103 0-2 .897-2 2v35c0 1.103.897 2 2 2s2-.897 2-2V38C40 36.897 39.103 36 38 36z"></path>
                                    </svg>
                                </button>

                            </form>
                        </td>              
                    </tr>
                @endforeach
            </tbody>
        </table>
        {{ $courses->links() }}

    </div>
@endsection
@section('style')
    <style>

        a.view {
            float: inline-end;
            cursor: pointer;
            padding: 10px;
            border-radius: 50px;
            background: #0a65cc;
            color: white;
            margin-bottom: 20px;
            transition: color 0.2s ease;

        }
       

        .view:hover {
            color: #ffffff; /* hover par white */
        }

        .labor-image{
            width: 50px !important;
            height: 50px;
            border-radius: 50%;
            object-fit: cover;
        }
        .switch {
            position: relative;
            display: inline-block;
            width: 35px;
            height: 19px;
        }

        /* Hide default HTML checkbox */
        .switch input {
            display: none;
        }

        /* The slider */
        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            -webkit-transition: .4s;
            transition: .4s;
        }

        .slider:before {
            position: absolute;
            content: "";
            height: 15px;
            width: 15px;
            left: 3px;
            bottom: 2px;
            background-color: white;
            -webkit-transition: .4s;
            transition: .4s;
        }

        input.success:checked+.slider {
            background-color: #28a745;
        }

        input:checked+.slider:before {
            -webkit-transform: translateX(15px);
            -ms-transform: translateX(15px);
            transform: translateX(15px);
        }

        /* Rounded sliders */
        .slider.round {
            border-radius: 34px;
        }

        .slider.round:before {
            border-radius: 50%;
        }
    </style>
@endsection
@section('script')
<script src="{{ asset('backend') }}/plugins/bootstrap-switch/js/bootstrap-switch.min.js"></script>


<script>
    jQuery(document).ready(function() {
        

        $(document).on("change",".status-switch",function() {
            var status = $(this).prop('checked') == true ? 1 : 0;
            var id = $(this).data('id');
            $.ajax({
                type: "GET",
                dataType: "json",
                url: '{{ route('admin.status.change') }}',
                data: {
                    'status': status,
                    'id': id
                },
                success: function(response) {
                    toastr.success(response.message, 'Success');
                }
            });

            if (status == 1) {
                $(`#status_${id}`).text("{{ __('activated') }}")
            }else{
                $(`#status_${id}`).text("{{ __('deactivated') }}")
            }
        });
    });
</script>
@endsection