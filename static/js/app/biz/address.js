/*
 * 芸众商城
 * 
 * @author 微赞科技 
 */
define(['jquery','core','func','tip'], function($,core,func,tip){
    var address = {
        current: {}
    };
 
    //添加地址
    address.add = function(){
        
        if($('#realname').isEmpty()){
            tip.show('请填写真实姓名!');
            return;
        }
        if(!$('#mobile').isMobile()){
            tip.show('请填写正确的手机号码!');
            return;
        }
        if($('#province').isEmpty() || $('#city').isEmpty() || $('#dist').isEmpty()){
            tip.show('请选择您的城市地区!');
            return;
        }
        if($('#address').isEmpty()){
            tip.show('请填写您的详细地址!');
            return;
        }
        var data={
            realname: $.trim( $('#realname').val() ),
            mobile: $.trim( $('#realname').val() ),
            province:  $.trim( $('#province').val() ),
            city:  $.trim( $('#city').val() ),
            dist:  $.trim( $('#dist').val() ),
            address:  $.trim( $('#address').val() )
        };
       core.json('shop/address/submit',data,function(ret){
                 if(ret.status==1){
                     tip.show('保存成功');
                 }
                 else{
                     tip.show( ret.msg );
                 }
         },true);
        
    }
    //删除地址
    address.delete = function(addressid){
        
        tip.confirm('确认从购物删除此地址吗?',function(){
            core.json('shop/address/delete',{addressid:addressid},function(ret){
                 if(ret.status==1){
                     tip.show('删除成功');
                     if(ret.result.defaultid){
                         $('#address_' + ret.result.defaultid).addClass('default');
                     }
                     $('#address_' + addressid).fadeOut(1000,function(){ 
                          $('#address_' + addressid).remove();
                     });
                 }
                 else{
                     tip.show('删除失败');
                 }
            },true);
        })
    }
    
    //选择地址
    address.select = function(addressid){
         core.json('shop/address/get',{addressid:addressid},function(ret){
                  
         });
    }
    
    return address;
    
});

