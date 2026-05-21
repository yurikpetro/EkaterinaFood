<x-filament-panels::page>
    <div class="prose prose-lg max-w-none dark:prose-invert">
        <ol class="space-y-4 text-base leading-relaxed">
            <li><strong>Новый заказ</strong> — появится в разделе «Заказы» со статусом «Новый». На главной панели видно, сколько заказов за сегодня.</li>
            <li><strong>Позвоните клиенту</strong> — уточните детали, если нужно. Телефон указан в карточке заказа.</li>
            <li><strong>Подтвердите</strong> — нажмите кнопку «Подтвердить» в списке или в карточке заказа.</li>
            <li><strong>Готовка</strong> — когда начали готовить, статус «Готовится», когда отдали — «Выполнен».</li>
            <li><strong>Меню</strong> — цены и блюда меняются в разделах «Категории» и «Блюда». Снятые с продажи блюда не видны на сайте.</li>
            <li><strong>Настройки</strong> — телефон, WhatsApp и тексты главной страницы в разделе «Настройки сайта».</li>
        </ol>
        <p class="mt-6 text-gray-600">Сайт для клиентов: <a href="{{ url('/') }}" target="_blank" class="text-primary-600 underline">{{ url('/') }}</a></p>
    </div>
</x-filament-panels::page>
