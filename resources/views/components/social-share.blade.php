@props(['title' => ''])

<div class="flex items-center gap-3">
    <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(request()->url()) }}" 
       target="_blank" 
       rel="noopener noreferrer"
       class="w-9 h-9 rounded-sm bg-gray-100 hover:bg-[#1877F2] text-gray-600 hover:text-white flex items-center justify-center transition-all border border-gray-200">
        <i class="fab fa-facebook-f text-sm"></i>
    </a>
    <a href="https://twitter.com/intent/tweet?url={{ urlencode(request()->url()) }}&text={{ urlencode($title) }}" 
       target="_blank" 
       rel="noopener noreferrer"
       class="w-9 h-9 rounded-sm bg-gray-100 hover:bg-[#1DA1F2] text-gray-600 hover:text-white flex items-center justify-center transition-all border border-gray-200">
        <i class="fab fa-twitter text-sm"></i>
    </a>
    <a href="https://www.linkedin.com/shareArticle?mini=true&url={{ urlencode(request()->url()) }}&title={{ urlencode($title) }}" 
       target="_blank" 
       rel="noopener noreferrer"
       class="w-9 h-9 rounded-sm bg-gray-100 hover:bg-[#0A66C2] text-gray-600 hover:text-white flex items-center justify-center transition-all border border-gray-200">
        <i class="fab fa-linkedin-in text-sm"></i>
    </a>
</div>
