// components/navigation/bottom-nav.tsx
"use client";

import React from "react";
import Link from "next/link";
import {
  HomeIcon,
  ListIcon,
  TrophyIcon,
  SearchIcon,
  UserIcon,
} from "lucide-react";
import { usePathname } from "next/navigation";

interface BottomNavProps {
  locale: string;
}

export function BottomNav({ locale }: BottomNavProps) {
  const pathname = usePathname();

  const navItems = [
    { href: `/${locale}`, icon: HomeIcon, label: "Home" },
    { href: `/${locale}/races`, icon: ListIcon, label: "Races" },
    { href: `/${locale}/records`, icon: TrophyIcon, label: "Records" },
    { href: `/${locale}/search`, icon: SearchIcon, label: "Search" },
    { href: `/${locale}/runners`, icon: UserIcon, label: "Runners" },
  ];

  return (
    <nav
      className="fixed bottom-0 inset-x-0 z-50 md:hidden flex justify-around
                 bg-[var(--color-header)] text-[var(--color-foreground)] 
                 border-t border-[var(--color-border)] py-2 backdrop-blur-sm"
    >
      {navItems.map(({ href, icon: Icon, label }) => {
        const isActive = pathname === href;
        return (
          <Link
            key={href}
            href={href}
            className={
              `flex flex-col items-center justify-center gap-0.5 px-3 py-1 ` +
              (isActive
                ? "text-[var(--color-primary)]"
                : "text-[var(--color-foreground)]/80 hover:text-[var(--color-foreground)]")
            }
          >
            <Icon className="h-5 w-5" />
            <span className="text-xs">{label}</span>
          </Link>
        );
      })}
    </nav>
  );
}
