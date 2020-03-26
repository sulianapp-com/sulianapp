/*
 * 芸众商城
 * 
 * @author 微赞科技 
 */
define(['jquery','core'], function($,core){
    var fav = {
        page: 0,
        pagesize:10,
    };
    
   //获取收藏
    fav.add = function(goodsid){
        
         core.json('shop/favorite/add',{goodsid:goodsid},function(ret){
             
         });
        
    }
    
    //添加收藏
    fav.add = function(goodsid){
        
         core.json('shop/favorite/delete',{goodsid:goodsid},function(ret){
                 if(ret.result==1){
                     tip.success('删除成功!',function(){})
                 }
                 else{
                     tip.success('删除失败!',function(){})
                 }
            },true);
        
    }
    
    //移除收藏
    fav.remove = function(goodsid){
        
         core.json('shop/favorite/remove',{goodsid:goodsid},function(ret){
               
            },true);
        
    }
    
    //删除收藏记录
    fav.delete = function(favid){
        
        tip.confirm('确认删除此收藏?',function(){
            core.json('shop/favorite/delete',{favid:favid},function(ret){
                 if(ret.result==1){
                     tip.success('删除成功!',function(){})
                 }
                 else{
                     tip.success('删除失败!',function(){})
                 }
            },true);
        })
    }
    
    return fav;
});
 

