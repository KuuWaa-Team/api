-- Script SQL pour insérer le template "Minimal"
-- À exécuter dans votre base de données

INSERT INTO event_template (
    name,
    code,
    price,
    image_path,
    has_animations,
    events_adapted,
    image_required,
    primary_color,
    secondary_color,
    width_image,
    heigh_image,
    image_rounded
) VALUES (
    'Minimal',
    'minimal',
    '0',
    '/templates/minimal/bgtop.png',
    1,
    'Mariage, Anniversaire, Cérémonie',
    0,
    '#8B6B1F',
    '#A77B00',
    NULL,
    NULL,
    0
);

