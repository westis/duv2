// app/[locale]/page.tsx – Landing / Home page
// All colours via OK‑LCH CSS variables; no Tailwind palette greys/reds.
// Hero stretches full‑width; inner content bounded by max-w-[96rem] .

import { useTranslations } from 'next-intl';
import { Link } from '@/i18n/navigation';
import { ArrowRight, CalendarIcon, TrophyIcon, UserIcon, SearchIcon } from 'lucide-react';

export default function HomePage() {
  const t = useTranslations('HomePage');

  /* Feature cards */
  const featureCards = [
    {
      icon: <CalendarIcon className="h-8 w-8" />,
      title: t('featureRacesTitle'),
      body: t('featureRacesDesc'),
    },
    {
      icon: <UserIcon className="h-8 w-8" />,
      title: t('featureRunnersTitle'),
      body: t('featureRunnersDesc'),
    },
    {
      icon: <TrophyIcon className="h-8 w-8" />,
      title: t('featureRecordsTitle'),
      body: t('featureRecordsDesc'),
    },
    {
      icon: <SearchIcon className="h-8 w-8" />,
      title: t('featureSearchTitle'),
      body: t('featureSearchDesc'),
    },
  ];

  /* Stats highlights */
  const stats = [
    { label: t('statCountries'), value: t('statCountriesValue') },
    { label: t('statRacesThisYear'), value: t('statRacesThisYearValue') },
    { label: t('statKmLogged'), value: t('statKmLoggedValue') },
    { label: t('statAvgKmPerRunner'), value: t('statAvgKmPerRunnerValue') },
  ];

  return (
    <div className="flex min-h-screen flex-col bg-[var(--color-background)] text-[var(--color-foreground)]">
      {/* --- HERO --- */}
      <section className="w-full bg-[var(--color-surface)]">
        <div className="mx-auto max-w-[96rem]  px-4 py-16 sm:px-6 lg:px-8">
          <h1 className="mb-6 text-4xl font-extrabold tracking-tight sm:text-5xl">
            {t('title')} <span className="text-[var(--color-primary)]">{t('titleAccent')}</span>
          </h1>
          <p className="mb-8 max-w-xl text-lg text-[var(--color-muted-foreground)]">
            {t('subtitle')}
          </p>
          <div className="flex flex-col gap-4 sm:flex-row">
            <Link
              href="/races"
              className="inline-flex items-center justify-center rounded-md bg-[var(--color-primary)] px-6 py-3 font-semibold text-[var(--color-primary-foreground)] hover:brightness-90"
            >
              {t('findRaces')} <ArrowRight className="ml-2 h-5 w-5" />
            </Link>
            <Link
              href="/about"
              className="inline-flex items-center justify-center rounded-md border border-[var(--color-border)] px-6 py-3 font-semibold hover:bg-[var(--color-muted)]/20"
            >
              {t('learnMore')}
            </Link>
          </div>
        </div>
      </section>

      {/* --- FEATURE CARDS --- */}
      <section className="mx-auto max-w-[96rem]  px-4 py-16 sm:px-6 lg:px-8">
        <div className="grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
          {featureCards.map((card) => (
            <div
              key={card.title}
              className="group rounded-lg border border-[var(--color-border)] bg-[var(--color-surface)] p-6 transition hover:border-[var(--color-primary)]"
            >
              <div className="mb-4 text-[var(--color-primary)]">{card.icon}</div>
              <h3 className="mb-1 font-semibold text-lg">{card.title}</h3>
              <p className="text-sm text-[var(--color-muted-foreground)]">{card.body}</p>
            </div>
          ))}
        </div>
      </section>

      {/* --- LIVE STATS --- */}
      <section className="w-full border-t border-[var(--color-border)] bg-[var(--color-surface)] py-16">
        <div className="mx-auto grid max-w-[96rem]  gap-8 px-4 text-center sm:grid-cols-2 md:grid-cols-4 sm:px-6 lg:px-8">
          {stats.map((s) => (
            <div key={s.label} className="space-y-2">
              <span className="block text-4xl font-extrabold">{s.value}</span>
              <span className="block text-sm font-medium uppercase tracking-wider text-[var(--color-muted-foreground)]">
                {s.label}
              </span>
            </div>
          ))}
        </div>
      </section>

      {/* --- CTA STRIPE --- */}
      <section className="w-full bg-[var(--color-primary)] py-16 text-[var(--color-primary-foreground)]">
        <div className="mx-auto max-w-[96rem] px-4 text-center sm:px-6 lg:px-8">
          <h2 className="mb-6 text-3xl font-extrabold">{t('ctaTitle')}</h2>
          <Link
            href="/signup"
            className="inline-flex items-center justify-center rounded-md bg-[var(--color-surface)] px-8 py-4 text-lg font-semibold text-[var(--color-primary)] hover:brightness-95"
          >
            {t('ctaButton')} <ArrowRight className="ml-2 h-5 w-5" />
          </Link>
        </div>
      </section>
    </div>
  );
}
