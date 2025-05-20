// components/layout/main-nav.tsx
"use client";

import React from "react";
import Link from "next/link";
import { usePathname } from "next/navigation";

interface NavItem {
  label: string;
  href: string;
  children?: NavItem[];
}

interface MainNavProps {
  items: NavItem[];
}

export function MainNav({ items }: MainNavProps) {
  const pathname = usePathname();

  return (
    <nav className="hidden md:flex items-center gap-1 lg:gap-2 xl:gap-6">
      {items.map((item, index) => {
        const isActive = pathname === item.href;

        return (
          <Link
            key={index}
            href={item.href}
            className={`relative px-3 py-2 text-xs md:text-sm xl:text-base font-medium rounded-md transition-colors hover:bg-accent/10 ${
              isActive
                ? "text-primary after:absolute after:bottom-0 after:left-0 after:right-0 after:h-0.5 after:bg-primary after:rounded-full"
                : "text-foreground/80"
            }`}
          >
            {item.label}
          </Link>
        );
      })}
    </nav>
  );
}
