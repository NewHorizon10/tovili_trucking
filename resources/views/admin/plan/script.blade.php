
<script>
$(document).ready(function() {
    $("select[name='is_free']").change(function(){
        if($(this).val() == '0'){
            $(".price-tag").show();
        }else{
            $(".price-tag").hide();
        }
    });
});
</script>