/*
 * 芸众商城
 * 
 */
define(['jquery','core'], function($,core){
    var cart = {
        page: 0,
        pagesize:10,
    };
    
    
    //添加
    cart.add = function(goodsid,optionid){
        
         core.json('shop/cart',{op:'add', id:goodsid,optionid:optionid},function(ret){
                 if(ret.status==1){
                     core.tip.show('添加成功')
                 }
                 else{
                     core.tip.show('添加失败')
                 }
            },true);
        
    }
    
    //移除收藏
    cart.remove = function(goodsid){
        
         core.json('shop/cartorite/remove',{goodsid:goodsid},function(ret){
               
            },true);
        
    }
    
    //删除收藏记录
    cart.delete = function(cartid){
        
        tip.confirm('确认删除此收藏?',function(){
            core.json('shop/cartorite/delete',{cartid:cartid},function(ret){
                 if(ret.result==1){
                     core.tip.show('删除成功!',function(){})
                 }
                 else{
                     core.tip.success('删除失败!',function(){})
                 }
            },true);
        })
    }
    
    return cart;
});
 

