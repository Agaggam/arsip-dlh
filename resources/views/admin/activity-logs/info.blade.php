{{-- Tips untuk fitur departemen --}}
<div x-data="{ showTips: true }" 
     x-show="showTips"
     class="mb-6 p-4 bg-blue-50/70 backdrop-blur-sm border border-blue-200/60 rounded-2xl shadow-sm relative">
    <div class="flex items-start gap-3">
        <div class="flex-shrink-0 mt-0.5">
            <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
        </div>
        <div class="flex-1">
            <h4 class="text-sm font-bold text-blue-800 uppercase tracking-wide">💡 Info Tracking System</h4>
            <ul class="mt-2 text-xs text-slate-600 space-y-1">
                <li>• <strong>User Auth "Register", "Login", "Logout"</strong> Tercatat</li>
                <li>• <strong>User Profile "Profile Update", "Profile Delete"</strong> Kurang Password</li>
                <li>• <strong>Manage User "Update_role", "Update_department", "Approval", "Delete"</strong> Tercatat</li>
                <li>• <strong>Manage Departemen "Create", "Update", "Delete"</strong> Tercatat</li>
                <li>• <strong>Manage Kategori Arsip "Create", "Update", "Delete"</strong> Tercatat</li>
                <!-- <li>• <strong>Manage Arsip "Create", "Update", "Delete"</strong> Tercatat</li> -->
                <!-- <li>• <strong>Restore Arsip "Restore"</strong> Tercatat</li> -->

            </ul>
        </div>
        <button @click="showTips = false" 
                class="flex-shrink-0 text-blue-400 hover:text-blue-600 transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
    </div>
</div>