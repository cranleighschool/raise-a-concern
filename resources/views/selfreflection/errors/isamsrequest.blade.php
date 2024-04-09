<x-selfreflection pageTitle="Error">
<h1>Error</h1>
    <div class="alert alert-danger">
        <p>The system had an issue communicating with ISAMS. Please try again a little later.</p>
        <p>{{ $message }}</p>
    </div>
    @if($xml)
        <div class="alert alert-info">
            <p>Here is the XML response from iSAMS:</p>
            <dl class="row">
                <dt class="col-sm-3">MessageId:</dt>
                <dd class="col-sm-9">{{ $xml['MessageId'] }}</dd>
                <dt class="col-sm-3">MessageName:</dt>
                <dd class="col-sm-9">{{ $xml['MessageName'] }}</dd>
                <dt class="col-sm-3">Title:</dt>
                <dd class="col-sm-9">{{ $xml['Title'] }}</dd>
                <dt class="col-sm-3">Description</dt>
                <dd class="col-sm-9">{{ $xml['Description'] }}</dd>
            </dl>
        </div>
        @endif

</x-selfreflection>
