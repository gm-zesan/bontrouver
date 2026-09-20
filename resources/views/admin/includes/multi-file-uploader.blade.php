{{--
| Multi File Uploader Alias
| Wrapper around admin.includes.multi-image-uploader with fileType='document'
--}}
@include('admin.includes.multi-image-uploader', array_merge([
    'name'        => 'documents[]',
    'label'       => 'Upload Documents',
    'modalTitle'  => 'Upload Engineering Documents',
    'helpText'    => 'PDF, DOC, DOCX, XLS, TXT up to 10MB each',
    'accept'      => '.pdf,.doc,.docx,.xls,.xlsx,.txt',
    'fileType'    => 'document',
    'maxSizeMb'   => 10,
    'icon'        => 'ri-file-upload-line',
    'applyBtnText'=> 'Apply Documents'
], $__data ?? []))
