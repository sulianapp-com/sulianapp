// 应用列表页面resources\views\admin\pluginslist.blade.php的js，因模拟ctrl+f搜索会把<script></script>标签里的内容搜索出来，导致出错，故迁出至此。
// $(function () { $("[data-toggle='tooltip']").tooltip(); });
    $(".plugin-a").mouseover(function(){
        $(this).find("a").css("display","inline");
    });
    $(".plugin-a").mouseleave(function () {
        $(this).find("a").css("display","none");
    })