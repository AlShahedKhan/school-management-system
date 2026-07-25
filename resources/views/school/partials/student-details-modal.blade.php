    <div id="detailsModal"
        class="premium-modal hidden fixed inset-0 z-[80] flex items-center justify-center bg-black/50 p-4">
        <div class="relative bg-white w-full max-w-5xl max-h-[70vh] lg:max-h-[90vh] overflow-hidden flex flex-col shadow-2xl border border-slate-200">
            <!-- Watermark Background -->
            <div class="absolute inset-0 flex items-center justify-center pointer-events-none select-none z-[0]" style="opacity: 0.08;">
                <img id="modalWatermarkLogo" src="" alt="watermark" style="max-width: 50%; max-height: 50%; object-fit: contain;" />
            </div>
            <div class="relative z-[1] px-6 py-2 lg:py-3 border-b border-slate-100 bg-slate-50 flex justify-between items-center">
                <h3 class="text-md text-slate-700 capitalize">Student Details</h3>
            </div>
            <form id="detailsForm" class="relative z-[1] overflow-y-auto p-6 custom-scrollbar">
                <input type="hidden" id="det_student_id">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <div class="space-y-4">
                        <h4 class="text-xs text-slate-600 border-b pb-1 capitalize">Academic information</h4>
                        <div>
                            <label class="text-[11px] text-gray-500 capitalize">Student id number</label>
                            <input name="student_id_number" class="form-input text-xs bg-gray-50" readonly>
                        </div>
                        <div class="grid grid-cols-1 gap-2">
                            <div>
                                <label class="text-[11px] text-gray-500 capitalize">Class</label>
                                <input name="class" class="form-input text-xs">
                            </div>
                            <div>
                                <label class="text-[11px] text-gray-500 capitalize">Section</label>
                                <input name="section" class="form-input text-xs">
                            </div>
                        </div>
                        <div class="grid grid-cols-1 gap-2">
                            <div>
                                <label class="text-[11px] text-gray-500 capitalize">Group</label>
                                <input name="group" class="form-input text-xs">
                            </div>
                            <div>
                                <label class="text-[11px] text-gray-500 capitalize">Session</label>
                                <input name="session" class="form-input text-xs">
                            </div>
                        </div>
                        <div class="pt-4 space-y-3">
                            <h4 class="text-xs text-slate-600 border-b pb-1 capitalize">Previous school</h4>
                            <div>
                                <label class="text-[11px] text-gray-500 capitalize">School name</label>
                                <input name="previous_school" class="form-input text-xs mt-1">
                            </div>
                            <div>
                                <label class="text-[11px] text-gray-500 capitalize">Last exam result</label>
                                <input name="last_exam_result" class="form-input text-xs mt-1">
                            </div>
                        </div>
                    </div>
                    <div class="space-y-4 border-x border-slate-50 px-0 md:px-4">
                        <h4 class="text-xs text-slate-600 border-b pb-1 capitalize">Personal details</h4>
                        <div>
                            <label class="text-[11px] text-gray-500 capitalize">Student name</label>
                            <input name="student_name" class="form-input text-xs">
                        </div>
                        <div>
                            <label class="text-[11px] text-gray-500 capitalize">Father's name</label>
                            <input name="father_name" class="form-input text-xs">
                        </div>
                        <div>
                            <label class="text-[11px] text-gray-500 capitalize">Mother's name</label>
                            <input name="mother_name" class="form-input text-xs">
                        </div>
                        <div>
                            <label class="text-[11px] text-gray-500 capitalize">Mobile number</label>
                            <input name="mobile" class="form-input text-xs">
                        </div>
                        <div class="pt-4">
                            <h4 class="text-xs text-slate-600 border-b pb-1 capitalize">Guardian information</h4>
                            <div id="guardian_details_area" class="bg-slate-50 p-3 mt-2 border border-slate-100 rounded-none space-y-3">
                                <div>
                                    <label class="text-[11px] text-gray-500 capitalize">Guardian name</label>
                                    <input name="g_name" class="form-input text-xs mt-1 rounded-none">
                                </div>
                                <div>
                                    <label class="text-[11px] text-gray-500 capitalize">Guardian mobile</label>
                                    <input name="g_mobile" class="form-input text-xs mt-1 rounded-none">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="space-y-4">
                        <h4 class="text-xs text-slate-600 border-b pb-1 capitalize">Current address</h4>
                        <div>
                            <label class="text-[11px] text-gray-500 capitalize">Village or area</label>
                            <input name="current_village" class="form-input text-xs mt-1">
                        </div>
                        <div class="grid grid-cols-1 gap-2">
                            <div>
                                <label class="text-[11px] text-gray-500 capitalize">Division</label>
                                <input name="current_division" class="form-input text-xs mt-1">
                            </div>
                            <div>
                                <label class="text-[11px] text-gray-500 capitalize">District</label>
                                <input name="current_district" class="form-input text-xs mt-1">
                            </div>
                        </div>
                        <div>
                            <label class="text-[11px] text-gray-500 capitalize">Upazila</label>
                            <input name="current_upazila" class="form-input text-xs mt-1">
                        </div>
                        <div class="pt-4 space-y-3">
                            <h4 class="text-xs text-slate-600 border-b pb-1 capitalize">Permanent address</h4>
                            <div>
                                <label class="text-[11px] text-gray-500 capitalize">Village or area</label>
                                <input name="permanent_village" class="form-input text-xs mt-1">
                            </div>
                            <div class="grid grid-cols-1 gap-2">
                                <div>
                                    <label class="text-[11px] text-gray-500 capitalize">Division</label>
                                    <input name="permanent_division" class="form-input text-xs mt-1">
                                </div>
                                <div>
                                    <label class="text-[11px] text-gray-500 capitalize">District</label>
                                    <input name="permanent_district" class="form-input text-xs mt-1">
                                </div>
                            </div>
                            <div>
                                <label class="text-[11px] text-gray-500 capitalize">Upazila</label>
                                <input name="permanent_upazila" class="form-input text-xs mt-1">
                            </div>
                        </div>
                        <div class="pt-4">
                            <h4 class="text-xs text-slate-600 border-b pb-1 capitalize">Application status</h4>
                            <div class="mt-2">
                                <label class="text-[11px] text-gray-500 capitalize">Update status</label>
                                <select name="status" class="form-input text-xs mt-1">
                                    <option value="pending">Pending</option>
                                    <option value="approved">Approved</option>
                                    <option value="rejected">Rejected</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
            <div class="relative z-[1] px-6 py-2 lg:py-3 border-t border-slate-100 bg-slate-50 flex justify-end gap-3">
                <button onclick="closeDetailsModal()" class="w-1/2 sm:w-auto sm:px-8 h-[32px] btn-outline-secondary border border-gray-200 text-[10px] tracking-normal capitalize transition-all hover:bg-gray-50 flex items-center justify-center whitespace-nowrap" style="border-radius: 0;">Cancel</button>
                <button onclick="updateFullDetails()" class="w-1/2 sm:w-auto sm:px-12 h-[32px] btn-outline-premium border border-gray-200 text-[10px] tracking-normal capitalize flex items-center justify-center whitespace-nowrap" style="border-radius: 0;">Update</button>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const sideLogo = document.getElementById('sideSchoolLogo');
            const watermarkImg = document.getElementById('modalWatermarkLogo');
            if (sideLogo && watermarkImg) {
                watermarkImg.src = sideLogo.src;
                
                const observer = new MutationObserver((mutations) => {
                    mutations.forEach((mutation) => {
                        if (mutation.type === 'attributes' && mutation.attributeName === 'src') {
                            watermarkImg.src = sideLogo.src;
                        }
                    });
                });
                observer.observe(sideLogo, { attributes: true });
            }
        });
    </script>
