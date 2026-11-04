# Компонент «Баннеры» для CMS RuDi
## Версия - 1.10.9

Самостоятельный компонент: баннеры, позиции, статистика показов и кликов.

## Установка

1. Скопируйте содержимое этой папки в корень сайта CMS RuDi:
   - `components/banners/`
   - `admin/components/banners/`
   - `admin/images/components/banners.png`
   - `languages/ru/admin/components/banners.php`
   - `languages/en/admin/components/banners.php`
   - `images/banners/cmsrudi-468x60.png`
   - `plugins/p_filters/filters/f_banners/filter.php`
2. В админке откройте **Компоненты → Установка компонентов** и установите **«Баннеры»**.
3. При установке автоматически создаются таблицы `cms_banners` и `cms_banner_hits`
   и добавляется демонстрационный баннер `CMS RuDi` (позиция `banner1`).

## Вывод баннера

В статье или модуле используйте выражение:

```text
{БАННЕР=banner1}
```

или для английского интерфейса:

```text
{BANNER=banner1}
```

Для работы выражений должен быть установлен и включён плагин **p_filters**
(файл фильтра уже входит в комплект).