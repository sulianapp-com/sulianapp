Vue.component('tinymce', {
    props: ['value','img'],
    data(){
        return{
            flag:true,
            hasInit: false,
            hasChange: false,
        }
    },
    watch:{
        value(val){
            if(this.flag){
                tinyMCE.activeEditor.setContent(val);
            }
            this.flag=true;
        },
        img(){
          if(this.img){
              tinyMCE.activeEditor.insertContent(`<img src="${this.img}" >`)
            }
        }
    },
    mounted: function(){
        var component = this;
        tinymce.init({
            selector: '#tinymceId',
            language: "zh_CN",
            hasChange: false,
            hasInit: false,
            menubar: false,
            body_class: 'panel-body ',
            object_resizing: false,
            end_container_on_empty_block: true,
            powerpaste_word_import: 'clean',
            code_dialog_height: 450,
            code_dialog_width: 1000,
            advlist_bullet_styles: 'square',
            advlist_number_styles: 'default',
            imagetools_cors_hosts: ['www.tinymce.com', 'codepen.io'],
            default_link_target: '_blank',
            link_title: false,
            nonbreaking_force_tab: true, // inserting nonbreaking space &nbsp; need Nonbreaking Space Plugin
            plugins: ['advlist anchor autolink autosave code codesample colorpicker colorpicker contextmenu directionality emoticons fullscreen hr image imagetools insertdatetime link lists media nonbreaking noneditable pagebreak paste preview print save searchreplace spellchecker tabfocus table template textcolor textpattern visualblocks visualchars wordcount'],
            toolbar: ['searchreplace bold italic underline strikethrough alignleft aligncenter alignright outdent indent  blockquote undo redo removeformat subscript superscript code codesample', 'hr bullist numlist link image charmap preview anchor pagebreak insertdatetime media table emoticons forecolor backcolor fullscreen'],
            init_instance_callback: editor => {
              if (this.value) {
                editor.setContent(this.value)
              }
              this.hasInit = true
              editor.on('NodeChange Change KeyUp SetContent', () => {
                this.hasChange = true
                this.$emit('input', editor.getContent())
              })
            },
            setup: function(editor) {
                editor.on('input undo redo execCommand', function(e) {
                    component.flag=false;
                    component.$emit('input', editor.getContent());
                }) 
            }
        });
    },
    methods:{
        setContent(value) {
          window.tinymce.get(this.tinymceId).setContent(value)
        },
        getContent() {
          window.tinymce.get(this.tinymceId).getContent()
        },
        destroyTinymce() {
          const tinymce = window.tinymce.get(this.tinymceId)
          if (this.fullscreen) {
            tinymce.execCommand('mceFullScreen')
          }

          if (tinymce) {
            tinymce.destroy()
          }
        },
    },
  template: `<div><textarea id="tinymceId" style="height:300px" v-model="value"></textarea></div>`
});
