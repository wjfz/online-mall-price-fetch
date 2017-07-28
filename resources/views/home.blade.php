@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Dashboard</div>

                <div class="panel-body">
                    You are logged in!
                </div>


                <form class="form-horizontal" action="{{ route('home') }}">
                    <div class="form-group">
                        <label for="sku" class="col-md-4 control-label">Amazon Sku</label>

                        <div class="col-md-6">
                            <input id="sku" class="form-control" name="sku" value="" required autofocus>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-8 col-md-offset-4">
                            <button class="btn btn-primary">
                                Login
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
