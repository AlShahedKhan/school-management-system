<x-modal.form
    id="smsSettingsModal"
    form-id="smsSettingsForm"
    title="SMS Settings"
    close-button-id="closeSmsSettingsModal"
    title-class="m-0 text-center font-semibold leading-tight text-slate-800 text-lg"
    panel-class="custom-scrollbar mx-auto my-auto w-full border border-slate-200 bg-white shadow-[0_18px_45px_rgba(15,23,42,0.24)] md:max-w-[540px]"
    panel-style="border-radius:4px; max-height:min(580px, calc(100dvh - 2.5rem));"
    fields-class="grid grid-cols-1 gap-3"
>
    <!-- Section Header -->
    <div class="flex items-center gap-2 mb-1 pb-1 border-b border-slate-100">
        <div style="width: 4px; height: 14px; background-color: #2563eb; border-radius: 9999px;" class="flex-shrink-0"></div>
        <span class="text-xs font-bold text-slate-700">Template Configuration</span>
    </div>

    <!-- Template Selector Dropdown -->
    <div class="relative">
        <x-input.dropdown-select id="sms_template_type" name="sms_type" placeholder="Select SMS Template..." />
        <x-input.floating-label for="sms_template_type" :floating="false">Template Event</x-input.floating-label>
    </div>

    <!-- Message Body Textarea -->
    <div class="relative">
        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Message Body</label>
        <textarea id="sms_template_body" name="template_body" rows="4" class="w-full text-xs p-2.5 border border-slate-200 focus:border-blue-600 outline-none font-mono text-slate-800 leading-relaxed custom-scrollbar bg-slate-50/50" placeholder="Enter message text..."></textarea>
        <div class="mt-1 flex justify-between text-[11px] text-slate-500 font-mono">
            <span>Characters: <strong id="sms_char_count" class="text-slate-800">0</strong></span>
            <span>Est. SMS: <strong id="sms_parts_count" class="text-blue-600">1</strong></span>
        </div>
    </div>

    <!-- Dynamic Variables Box -->
    <div class="bg-slate-50 border border-slate-200 p-2.5 rounded-sm">
        <div class="flex items-center gap-1.5 mb-1.5">
            <i class="fas fa-magic text-blue-600 text-[10px]"></i>
            <span class="text-[11px] font-bold text-slate-700 uppercase tracking-wider">Clickable Variables (ক্লিক করলে মেসেজে বসবে)</span>
        </div>
        <div id="sms_placeholders_container" class="flex flex-wrap gap-1.5 min-h-[28px] items-center">
            <span class="text-xs text-slate-400 italic">Select a template to view variables</span>
        </div>
    </div>

    <!-- Status Selector -->
    <div class="relative">
        <x-input.dropdown-select id="sms_template_status" name="status" placeholder="Select Status...">
            <option value="Active">Active (SMS পাঠানো চালু থাকবে)</option>
            <option value="Inactive">Inactive (SMS পাঠানো বন্ধ থাকবে)</option>
        </x-input.dropdown-select>
        <x-input.floating-label for="sms_template_status" :floating="false">Sending Status</x-input.floating-label>
    </div>

    <x-slot:footer>
        <div class="grid grid-cols-2 gap-3 border-t border-slate-200 bg-slate-50 px-6 py-3">
            <x-button.secondary id="closeSmsSettingsModalBtn" type="button" class="w-full">
                Cancel
            </x-button.secondary>
            <x-button.primary id="saveSmsSettingsBtn" type="button" class="w-full">
                Save Settings
            </x-button.primary>
        </div>
    </x-slot:footer>
</x-modal.form>

@include('school.partials.js.sms-settings-js')
