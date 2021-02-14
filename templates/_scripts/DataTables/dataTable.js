//JQuery-DaraTables common cofig file

$(document).ready(function () {
     // Setup - add a text input to each footer cell
    $('#lstindex tfoot th').each( function () {
        var title = $(this).text();
        $(this).html( '<input type="text" placeholder="Search '+title+'" />' );
    } );

    var table=$('#lstindex').DataTable({
    "pagingType": "full_numbers",
    "ordering": true,
    "stateSave": true,
    /*"scrollX": true //horizontal scrollbar
    "dom": '<"top"iflp<"clear">>rt<"bottom"iflp<"clear">>' //multiple paginator (top/bottom)
    "language": {
        "url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/French.json"
    }
    "searching": false, //search disabled
    */
   initComplete: function () {
    // Apply the search
    this.api().columns().every( function () {
        var that = this;

        $( 'input', this.footer() ).on( 'keyup change clear', function () {
            if ( that.search() !== this.value ) {
                that
                    .search( this.value )
                    .draw();
            }
        } );
    } );
}
    
    });
    $('button.toggle-vis').on( 'click', function (e) {
        e.preventDefault();
         // Get the column API object
        var column = table.column( $(this).attr('data-column') );
         // Toggle the visibility
        column.visible( ! column.visible() );
       } );
    });
        
  /*********** */
 