<footer class="bg-slate-900 text-slate-300 border-t border-slate-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-10">
            <div>
                <div class="flex items-center space-x-3 mb-5">
                    <div class="w-12 h-12 rounded-xl bg-indigo-600 flex items-center justify-center shadow-lg">
                        <i class="fa-solid fa-graduation-cap text-white text-2xl"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-white">Astha Academics</h3>
                        <p class="text-sm text-slate-400">Smart Education System</p>
                    </div>
                </div>
                <p class="text-sm leading-7 text-slate-400">
                    আধুনিক শিক্ষা প্রতিষ্ঠানের জন্য সম্পূর্ণ অটোমেটেড ERP সফটওয়্যার।
                    ছাত্র, শিক্ষক, উপস্থিতি, ফি, পরীক্ষা, অ্যাকাউন্টিং এবং AI
                    অটো কলিংসহ সকল কার্যক্রম একটি প্ল্যাটফর্মে পরিচালনা করুন।
                </p>
            </div>
            <div>
                <h4 class="text-lg font-bold text-white mb-5">যোগাযোগ</h4>
                <ul class="space-y-4 text-sm">
                    <li class="flex items-start">
                        <i class="fa-solid fa-location-dot text-indigo-400 mt-1 mr-3"></i>
                        <span>
                            জামির প্লাজা (২য় তলা)<br>
                            দক্ষিণ ছায়াবীথি রোড, জোড়পুকুর পাড়<br>
                            গাজীপুর মহানগর, গাজীপুর।
                        </span>
                    </li>
                    <li class="flex items-center">
                        <i class="fa-solid fa-phone text-indigo-400 mr-3"></i>
                        <a href="tel:+8801337225555" class="hover:text-white transition">01337225555</a>
                    </li>
                    <li class="flex items-center">
                        <i class="fa-solid fa-envelope text-indigo-400 mr-3"></i>
                        <a href="mailto:support@asthaacademics.com" class="hover:text-white transition">support@asthaacademics.com</a>
                    </li>
                </ul>
            </div>
            <div>
                <h4 class="text-lg font-bold text-white mb-5">গুরুত্বপূর্ণ লিংক</h4>
                <ul class="space-y-3 text-sm">
                    <li><a href="{{ route('home') }}#about" class="hover:text-indigo-400 transition">আমাদের লক্ষ্য</a></li>
                    <li><a href="{{ route('home') }}#services" class="hover:text-indigo-400 transition">সার্ভিসসমূহ</a></li>
                    <li><a href="{{ route('home') }}#features" class="hover:text-indigo-400 transition">ফিচার্স</a></li>
                    <li><a href="{{ route('home') }}#why-us" class="hover:text-indigo-400 transition">কেন আমাদের বেছে নিবেন</a></li>
                    <li><a href="{{ route('home') }}#contact" class="hover:text-indigo-400 transition">যোগাযোগ</a></li>
                </ul>
            </div>
            <div>
                <h4 class="text-lg font-bold text-white mb-5">অফিস সময়</h4>
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between border-b border-slate-800 pb-2">
                        <span>শনিবার - বৃহস্পতিবার</span>
                        <span class="font-semibold text-white">৯:০০ AM - ৬:০০ PM</span>
                    </div>
                    <div class="flex justify-between border-b border-slate-800 pb-2">
                        <span>শুক্রবার</span>
                        <span class="font-semibold text-red-400">বন্ধ</span>
                    </div>
                </div>
                <div class="flex space-x-4 mt-6">
                    <a href="https://www.facebook.com/share/199jXu2jfd/" target="_blank" rel="noopener noreferrer" aria-label="Facebook" class="w-10 h-10 rounded-lg bg-slate-800 hover:bg-indigo-600 transition flex items-center justify-center">
                        <i class="fab fa-facebook-f"></i>
                    </a>
                    <a href="https://youtube.com/@asthaacademics?si=kpFCQZQGEBOGVC1u" target="_blank" rel="noopener noreferrer" aria-label="YouTube" class="w-10 h-10 rounded-lg bg-slate-800 hover:bg-indigo-600 transition flex items-center justify-center">
                        <i class="fab fa-youtube"></i>
                    </a>
                    <a href="https://wa.me/8801337225555" target="_blank" rel="noopener noreferrer" aria-label="WhatsApp" class="w-10 h-10 rounded-lg bg-slate-800 hover:bg-indigo-600 transition flex items-center justify-center">
                        <i class="fab fa-whatsapp"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
    <div class="border-t border-slate-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 flex flex-col md:flex-row justify-between items-center gap-4">
            <p class="text-sm text-slate-500 text-center md:text-left">
                © {{ date('Y') }} <span class="text-white font-semibold">Astha Academics</span>. সর্বস্বত্ব সংরক্ষিত।
            </p>
            <p class="text-sm text-slate-500 text-center md:text-right">
                Designed &amp; Developed by <span class="text-indigo-400 font-semibold">Astha Academics Team</span>
            </p>
        </div>
    </div>
</footer>
