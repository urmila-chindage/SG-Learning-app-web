<style>
    .alert-modal-new .modal-header{
        border-bottom: 0;
        float: right;
        width: 40px;
        height: 40px;
        background: none;
    }
    .alert-modal-new .modal-header .close {
        font-size: 24px;
        color: #838383;
        right: 13px;
        top: 9px;
        z-index: 9;
        position: relative;
    }
</style>
<!-- modal starts -->
<div id="common_modal" class="modal fade ofabee-modal alert-modal-new" role="dialog" aria-hidden="true" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content ofabee-modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">×</button>
            </div>
            <div class="modal-body">
                <div class="col-md-12 text-center">
                    <div id="common_modal_svg" class="icon-align">
                        
                    </div>
                    <!-- <h2 class="alert-title orange-text">Warning</h2> -->
                    <p id="common_modal_content" class="alert-text"></p>
                </div>
            </div>
            <div class="modal-footer ofabee-modal-footer text-center-alter">
                <!-- <button type="button" class="btn ofabee-dark" data-dismiss="modal">Cancel</button> -->
                <button id="change_pass_btn" type="button" class="btn btn-warning alert-btn" data-dismiss="modal">OK</button>
            </div>
        </div>
    </div>
</div>
<!-- modal ends -->

<script>
    function showCommonModal(heading = '',message = '',type = 0){
        var svg = '';
        switch(type){
            case 1:  //Success
                svg = `<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" enable-background="new 0 0 128 128" height="60px" id="Layer_1" version="1.1" viewBox="0 0 128 128" width="60px" fill="#00aa47" xml:space="preserve"><g><g><path d="M85.263,46.49L54.485,77.267L42.804,65.584c-0.781-0.782-2.047-0.782-2.828-0.002c-0.781,0.782-0.781,2.048,0,2.829    l14.51,14.513l33.605-33.607c0.781-0.779,0.781-2.046,0-2.827C87.31,45.708,86.044,45.708,85.263,46.49z M64.032,13.871    c-27.642,0-50.129,22.488-50.129,50.126c0.002,27.642,22.49,50.131,50.131,50.131h0.004c27.638,0,50.123-22.489,50.123-50.131    C114.161,36.358,91.674,13.871,64.032,13.871z M64.038,110.128h-0.004c-25.435,0-46.129-20.694-46.131-46.131    c0-25.434,20.693-46.126,46.129-46.126s46.129,20.693,46.129,46.126C110.161,89.434,89.471,110.128,64.038,110.128z"/></g></g></svg>`;
            break;

            case 2:  //Error
                svg = `<svg enable-background="new 0 0 128 128" height="60px" id="Layer_1" version="1.1" viewBox="0 0 128 128" width="60px" fill="#f44" xml:space="preserve" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"><g><g><path d="M84.815,43.399c-0.781-0.782-2.047-0.782-2.828,0L64.032,61.356L46.077,43.399c-0.781-0.782-2.047-0.782-2.828,0    c-0.781,0.781-0.781,2.047,0,2.828l17.955,17.957L43.249,82.141c-0.781,0.78-0.781,2.047,0,2.828    c0.391,0.39,0.902,0.585,1.414,0.585s1.023-0.195,1.414-0.585l17.955-17.956l17.955,17.956c0.391,0.39,0.902,0.585,1.414,0.585    s1.023-0.195,1.414-0.585c0.781-0.781,0.781-2.048,0-2.828L66.86,64.184l17.955-17.957C85.597,45.447,85.597,44.18,84.815,43.399z     M64.032,14.054c-27.642,0-50.129,22.487-50.129,50.127c0.002,27.643,22.491,50.131,50.133,50.131    c27.639,0,50.125-22.489,50.125-50.131C114.161,36.541,91.674,14.054,64.032,14.054z M64.036,110.313h-0.002    c-25.435,0-46.129-20.695-46.131-46.131c0-25.435,20.693-46.127,46.129-46.127s46.129,20.693,46.129,46.127    C110.161,89.617,89.47,110.313,64.036,110.313z"/></g></g></svg>`;
            break;

            default : //Info
                svg = `<svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
                                width="60px" height="60px" fill="#f78700" viewBox="0 0 128 128" enable-background="new 0 0 128 128" xml:space="preserve">
                        <circle fill="none" stroke="#f78700" stroke-width="5" stroke-miterlimit="10" cx="64" cy="64" r="47.304"/>
                        <path d="M67.375,80.041c0,1.496-1.287,2.709-2.875,2.709l0,0c-1.588,0-2.875-1.213-2.875-2.709V37.876
                            c0-1.496,1.287-2.709,2.875-2.709l0,0c1.588,0,2.875,1.213,2.875,2.709V80.041z"/>
                        <path d="M67.542,91.382c0,1.681-1.362,3.042-3.042,3.042l0,0c-1.68,0-3.042-1.361-3.042-3.042v-0.264
                            c0-1.681,1.362-3.042,3.042-3.042l0,0c1.68,0,3.042,1.361,3.042,3.042V91.382z"/>
                        </svg>`;
            break;
        }
        $('#common_modal_svg').html(svg);
        $('#common_modal_content').html(message);
        $('#common_modal').modal('show');
    }
</script>