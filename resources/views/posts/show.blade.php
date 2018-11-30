@extends('layouts.app')

@section('content')
    <a href="/posts" class="btn btn-info mb-4 text-white">Go Back</a>
    <h1 class="mb-4">{{ $post->title }}</h1>
    <div>
        <h5 class="mt-2 mb-4">{!! $post->body !!}</h5>
    </div>
    <hr>
    <p>Written on {{ $post->created_at }}</p>
    <hr>
    <a href="/posts/{{ $post->id }}/edit" class="btn btn-success text-white">Edit</a>

    {!! Form::open(['action' => ['PostsController@destroy', $post->id], 'method' =>'POST', 'class' => 'float-right']) !!}
        {!! Form::hidden('_method', 'DELETE') !!}
        {!! Form::submit('Delete', ['class' => 'btn btn-danger']) !!}
    {!! Form::close() !!}
@endsection