@extends('frontend.layouts.app')
@section('script')
<script src="https://cdn.tailwindcss.com"></script>
@endsection

@section('main')
    <div class="max-w-2xl mx-auto py-12">
        <div class="md:flex md:items-center md:justify-between mb-6">
            <div class="flex-1 min-w-0">
                <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
                    Add New Course
                </h2>
            </div>
        </div>

        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <form action="{{ route('courses.store') }}" method="POST">
                    @csrf

                    <div class="space-y-6">
                        <div>
                            <label for="title" class="block text-sm font-medium text-gray-700">Course Title</label>
                            <div class="mt-1">
                                <input type="text" name="title" id="title" value="{{ old('title') }}"
                                    class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md p-2 border"
                                    placeholder="e.g. Mastering Laravel">
                            </div>
                            @error('title')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                            <div class="mt-1">
                                <textarea id="description" name="description" rows="3"
                                    class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md p-2 border"
                                    placeholder="Brief overview of the course content...">{{ old('description') }}</textarea>
                            </div>
                            @error('description')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="category_id" class="block text-sm font-medium text-gray-700">Category</label>
                            <div class="mt-1">
                                <select id="category_id" name="category_id"
                                    class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md p-2 border">
                                    <option value="">-- Select Category --</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            @error('category_id')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="price" class="block text-sm font-medium text-gray-700">Course Price</label>
                            <div class="mt-1">
                                <input type="number" name="price" id="price" value="{{ old('price') }}"
                                    class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md p-2 border"
                                    placeholder="e.g. 50000">
                            </div>
                            @error('price')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- <div>
                            <label for="discounted_price" class="block text-sm font-medium text-gray-700">Course Discounted Price</label>
                            <div class="mt-1">
                                <input type="number" name="discounted_price" id="discounted_price" value="{{ old('discounted_price') }}"
                                    class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md p-2 border"
                                    placeholder="e.g. 20000">
                            </div>
                            @error('discounted_price')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div> --}}


                        <div>
                            <label for="thumbnail_url" class="block text-sm font-medium text-gray-700">Thumbnail URL</label>
                            <div class="mt-1">
                                <input type="url" name="thumbnail_url" id="thumbnail_url" value="{{ old('thumbnail_url') }}"
                                    class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md p-2 border"
                                    placeholder="https://example.com/image.jpg">
                            </div>
                            <p class="mt-2 text-sm text-gray-500">Provide a direct link to an image file.</p>
                            @error('thumbnail_url')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="external_link" class="block text-sm font-medium text-gray-700">External Course
                                Link</label>
                            <div class="mt-1">
                                <input type="url" name="external_link" id="external_link" value="{{ old('external_link') }}"
                                    class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md p-2 border"
                                    placeholder="https://udemy.com/course/...">
                            </div>
                            <p class="mt-2 text-sm text-gray-500">The URL where users will be redirected to view the course.
                            </p>
                            @error('external_link')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex justify-end">
                            <a href="{{ route('courses.index') }}"
                                class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 mr-3">
                                Cancel
                            </a>
                            <button type="submit"
                                class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                Save Course
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection