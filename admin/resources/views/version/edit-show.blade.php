<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- Latest compiled JavaScript -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>
   <div class="container">
    <form action="{{ route('version.updateData') }}" method="post">
        @csrf
        <input type="hidden" name="id" value="{{$data->id}}">
        <div class="mb-3 mt-3">
          <label for="" class="form-label">Device Type:</label>
          <input type="text" class="form-control" id="" name="device_type" value="{{$data->device_type}}">
        </div>
        <div class="mb-3">
          <label for="" class="form-label">Version No:</label>
          <input type="text" class="form-control" id="" name="version_no"value="{{$data->version_no}}">
        </div>
        <div class="form-check mb-3 form-label">
          <label class="form-check-label"> status </label>
            <input type="radio" name="status" value="1" id="on" <?php if($data->status== "1"){ echo "Checked" ; } else{ echo "";}?>>
            <input type="radio" name="status" value="0" id="off"  <?php if($data->status== "0"){ echo "Checked" ; } else{ echo "";}?>>
          
        
        </div>
        <button type="submit" class="btn btn-primary">Submit</button>
    </form>
   </div>
 
</body>
</html>