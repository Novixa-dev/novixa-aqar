@props(['floorPlanData' => null])

@php
    $floorPlanData = $floorPlanData ?? $attributes->get('floor-plan-data');
@endphp

@if($floorPlanData && is_array($floorPlanData) && isset($floorPlanData['image']))
<div class="floor-plan-viewer my-6">
    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">{{ app()->getLocale() === 'ar' ? 'المخطط التفاعلي' : 'Interactive Floor Plan' }}</h3>
    
    <div x-data="floorPlanViewer(@js($floorPlanData))" class="bg-white dark:bg-gray-800 rounded-lg border border-gray-300 dark:border-gray-600 p-4">
        <div class="relative">
            <canvas 
                x-ref="canvas"
                class="w-full border border-gray-200 dark:border-gray-700 rounded"
                @click="handleCanvasClick($event)"
                @mousemove="handleMouseMove($event)"
            ></canvas>
        </div>
        
        <!-- Annotation Details Modal -->
        <div x-show="selectedAnnotation" 
             x-cloak
             class="mt-4 p-4 bg-[#8C7869]/10 border border-[#8C7869]/20 rounded-lg"
        >
            <div class="flex items-center justify-between">
                <div>
                    <span class="inline-block w-6 h-6 text-[#8C7869]"><svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 21v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21m0 0h4.5V3.545M12.75 21h7.5V10.75M2.25 21h1.5m18 0h-18M2.25 9l4.5-1.636M18.75 3l-1.5.545m0 6.205l3 1m1.5.5l-1.5-.5M6.75 7.364V3h-3v18m3-13.636l10.5-3.819" /></svg></span>
                    <span class="ms-2 font-semibold text-gray-900 dark:text-white" x-text="selectedAnnotation?.label"></span>
                </div>
                <button @click="selectedAnnotation = null" aria-label="{{ app()->getLocale() === 'ar' ? 'إغلاق' : 'Close' }}" class="text-gray-500 hover:text-gray-700 dark:hover:text-gray-300">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Legend -->
        <div class="mt-4 flex gap-4 flex-wrap">
            <div class="flex items-center gap-2">
                <div class="w-6 h-6 rounded-full bg-[#8C7869] border-2 border-[#7A6759]"></div>
                <span class="text-sm text-gray-700 dark:text-gray-300">{{ app()->getLocale() === 'ar' ? 'غرفة' : 'Room' }}</span>
            </div>
            <div class="flex items-center gap-2">
                <div class="w-6 h-6 rounded-full bg-red-500 border-2 border-red-800"></div>
                <span class="text-sm text-gray-700 dark:text-gray-300">{{ app()->getLocale() === 'ar' ? 'نقطة اهتمام' : 'Point of Interest' }}</span>
            </div>
        </div>
    </div>
</div>

<script>
    function floorPlanViewer(floorPlanData) {
        return {
            floorPlanData: floorPlanData,
            canvas: null,
            ctx: null,
            floorPlanImage: null,
            selectedAnnotation: null,
            hoveredAnnotation: null,
            
            init() {
                this.canvas = this.$refs.canvas;
                this.ctx = this.canvas.getContext('2d');
                
                if (this.floorPlanData && this.floorPlanData.image) {
                    this.loadImage(this.floorPlanData.image);
                }
            },
            
            loadImage(imageSrc) {
                const img = new Image();
                img.onload = () => {
                    this.floorPlanImage = img;
                    
                    // Set canvas size to match container while maintaining aspect ratio
                    const maxWidth = this.canvas.parentElement.clientWidth;
                    const scale = maxWidth / img.width;
                    
                    this.canvas.width = img.width;
                    this.canvas.height = img.height;
                    this.canvas.style.maxWidth = '100%';
                    this.canvas.style.height = 'auto';
                    
                    this.redraw();
                };
                img.src = imageSrc;
            },
            
            handleCanvasClick(event) {
                const annotation = this.getAnnotationAtPoint(event);
                if (annotation) {
                    this.selectedAnnotation = annotation;
                } else {
                    this.selectedAnnotation = null;
                }
                this.redraw();
            },
            
            handleMouseMove(event) {
                const annotation = this.getAnnotationAtPoint(event);
                this.hoveredAnnotation = annotation;
                this.canvas.style.cursor = annotation ? 'pointer' : 'default';
                this.redraw();
            },
            
            getAnnotationAtPoint(event) {
                const rect = this.canvas.getBoundingClientRect();
                const scaleX = this.canvas.width / rect.width;
                const scaleY = this.canvas.height / rect.height;
                const x = (event.clientX - rect.left) * scaleX;
                const y = (event.clientY - rect.top) * scaleY;
                
                const annotations = this.floorPlanData.annotations || [];
                for (let i = annotations.length - 1; i >= 0; i--) {
                    const annotation = annotations[i];
                    const radius = annotation.type === 'room' ? 20 : 15;
                    const distance = Math.sqrt(Math.pow(x - annotation.x, 2) + Math.pow(y - annotation.y, 2));
                    
                    if (distance <= radius) {
                        return annotation;
                    }
                }
                return null;
            },
            
            redraw() {
                if (!this.ctx || !this.floorPlanImage) return;
                
                // Clear canvas
                this.ctx.clearRect(0, 0, this.canvas.width, this.canvas.height);
                
                // Draw floor plan image
                this.ctx.drawImage(this.floorPlanImage, 0, 0);
                
                // Draw annotations
                const annotations = this.floorPlanData.annotations || [];
                annotations.forEach((annotation) => {
                    const isSelected = this.selectedAnnotation === annotation;
                    const isHovered = this.hoveredAnnotation === annotation;
                    
                    if (annotation.type === 'room') {
                        // Draw room marker (circle)
                        this.ctx.fillStyle = isSelected || isHovered ? 'rgba(59, 130, 246, 0.9)' : 'rgba(59, 130, 246, 0.7)';
                        this.ctx.beginPath();
                        this.ctx.arc(annotation.x, annotation.y, 20, 0, 2 * Math.PI);
                        this.ctx.fill();
                        this.ctx.strokeStyle = isSelected ? '#1e3a8a' : '#1e40af';
                        this.ctx.lineWidth = isSelected ? 3 : 2;
                        this.ctx.stroke();
                    } else {
                        // Draw marker (pin)
                        this.ctx.fillStyle = isSelected || isHovered ? 'rgba(239, 68, 68, 0.9)' : 'rgba(239, 68, 68, 0.7)';
                        this.ctx.beginPath();
                        this.ctx.arc(annotation.x, annotation.y, 15, 0, 2 * Math.PI);
                        this.ctx.fill();
                        this.ctx.strokeStyle = isSelected ? '#7f1d1d' : '#991b1b';
                        this.ctx.lineWidth = isSelected ? 3 : 2;
                        this.ctx.stroke();
                    }
                    
                    // Draw label
                    this.ctx.fillStyle = '#000';
                    this.ctx.font = 'bold 14px Arial';
                    this.ctx.fillText(annotation.label, annotation.x + 25, annotation.y + 5);
                });
            }
        }
    }
</script>

<style>
    [x-cloak] { display: none !important; }
</style>
@endif
