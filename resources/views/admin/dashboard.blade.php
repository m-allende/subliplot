@extends('adminlte::page') {{-- si ya tienes AdminLTE; si no, usa un layout simple por ahora --}}
@section('title', 'Panel Admin')
@section('content_header')
  <h1>Panel de Administración</h1>
@endsection
@section('content')
  <p>Bienvenido, {{ auth()->user()->name }}.</p>
@endsection
