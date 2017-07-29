@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Dashboard</div>

                <div class="panel-body">
                    @if (!empty($sku))
                        {{$sku}} add {{$result}}
                    @endif
                </div>


                <form class="form-horizontal" method="POST" action="{{ route('addSku') }}">
                    {{ csrf_field() }}

                    <div class="form-group">
                        <label for="source" class="col-md-4 control-label">网站</label>

                        <div class="col-md-6">
                            <select id="source" name="source" class="form-control" title="选择网站">
                                @foreach ($sources as $source => $sourceRemark)
                                    <option value="{{$source}}">{{$sourceRemark}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="sku" class="col-md-4 control-label">Sku</label>

                        <div class="col-md-6">
                            <input id="sku" class="form-control" name="sku" value="" required autofocus>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-8 col-md-offset-4">
                            <button class="btn btn-primary">
                                添加
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
