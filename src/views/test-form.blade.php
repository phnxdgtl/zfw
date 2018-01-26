<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>ZFW</title>

    <!-- Bootstrap core CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <style>
      body {
        padding-top: 5rem;
      }
    </style>
  </head>

  <body>

    <nav class="navbar navbar-expand-md navbar-dark bg-dark fixed-top">
      <a class="navbar-brand" href="#">Navbar</a>
      <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarsExampleDefault" aria-controls="navbarsExampleDefault" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse" id="navbarsExampleDefault">
        <ul class="navbar-nav mr-auto">
          <li class="nav-item active">
            <a class="nav-link" href="#">Home <span class="sr-only">(current)</span></a>
          </li>
      </div>
    </nav>

    <main role="main" class="container">

      <div class="row">
          <div class="col-md">
            <h1>ZFW Form Test</h1>
            <hr>
            @if (!empty($thanks))
                <div class="alert alert-success">
                  <strong>Thanks!</strong> We'll be in touch. <a href="{{ route('zfw-test') }}">Back</a>
                </div>
            @else
              <form method="post" {!! Zfw::form('test') !!}>
                  {{ csrf_field() }}
                  <div class="form-group">
                      <label for="name">Name</label>
                      <input type="text" class="form-control" name="name" id="name" placeholder="Enter your name" value="{{ old('name') }}">
                  </div>
                  <div class="form-group">
                      <label for="email">Email address</label>
                      <input type="email" class="form-control" id="email" name="email" placeholder="Enter email"  value="{{ old('email') }}">
                  </div>
                  <div class="form-group">
                    <label for="message">Message</label>
                    <textarea  class="form-control" id="message" name="message" rows="3">{{ old('message') }}</textarea>
                  </div>

                  <div class="form-group">
                    <p>Options:</p>
                    @for ($i = 1; $i <= 3; $i++)
                    <div class="form-check">
                      <input class="form-check-input" name="options[]" type="checkbox" value="Option {{ $i }}" id="option-{{ $i }}" @if (is_array(old('options')) && in_array("Option $i", old('options'))) checked="checked" @endif>
                      <label class="form-check-label" for="option-{{ $i }}">
                        Option {{ $i }}
                      </label>
                    </div>
                    @endfor
                  </div>

                  <div class="form-check">
                      <input type="checkbox" class="form-check-input" id="optin" name="optin" value="1"  @if (old('optin')) checked="checked" @endif>
                      <label class="form-check-label" for="optin"> Opt in</label>
                  </div>
                  <hr>
                  <button type="submit" class="btn btn-primary">Submit</button>
              </form>
            @endif
        </div>
      </div>

    </main><!-- /.container -->

    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>

    {!!  Zfw::renderJS() !!}

  </body>
</html>
